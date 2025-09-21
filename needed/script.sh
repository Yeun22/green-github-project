cat > ~/scripts/auto-commit.sh <<'BASH'
#!/usr/bin/env bash
set -euo pipefail

# ====== CONFIG ======
export TZ="Europe/Paris"
export PATH="/usr/local/bin:/usr/bin:/bin"

REPO_DIR="$HOME/www/green-github-project"   # chemin du clone
BRANCH="main"                      # ou "master"
COUNTER_FILE="data/counter.txt"
STATS_FILE="public/stats.json"

SSH_KEY="$HOME/.ssh/id_ed25519_github"
GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes"
export GIT_SSH_COMMAND

# Assure known_hosts pour cron
ssh-keyscan -H github.com >> "$HOME/.ssh/known_hosts" 2>/dev/null || true

cd "$REPO_DIR"

# Sécurise l'état, récupère les dernières modifs
git reset --hard
git clean -fd
git checkout "$BRANCH"
git pull --rebase origin "$BRANCH"

# ----- Fonction: calcule le streak (jours consécutifs) depuis un set de dates (YYYY-MM-DD) -----
has_date() {
  local needle="$1"; shift
  printf "%s\n" "$@" | grep -qx "$needle"
}

compute_streak() {
  local include_today="$1" # "yes" / "no"
  local dates; dates=$(git log --since="365 days ago" --date=short --pretty=format:%ad | sort -u)
  local streak=0
  local i=0
  while :; do
    local day
    day=$(date -d "$i day ago" +%Y-%m-%d)
    if [[ "$i" -eq 0 && "$include_today" == "no" ]]; then
      i=$((i+1))
      continue
    fi
    if has_date "$day" "$dates"; then
      streak=$((streak+1))
      i=$((i+1))
    else
      break
    fi
  done
  echo "$streak"
}

today=$(date +%Y-%m-%d)
today_has_commits=$(git log --since="$today 00:00" --until="$today 23:59" --pretty=format:%H | wc -l | tr -d ' ')

streak_incl_today=$(compute_streak "yes")
streak_excl_today=$(compute_streak "no")

# Total avant commit
total_before=$(git rev-list --count HEAD)

# ----- Modifie le fichier simple (counter) -----
if [[ ! -f "$COUNTER_FILE" ]]; then
  mkdir -p "$(dirname "$COUNTER_FILE")"
  echo "0" > "$COUNTER_FILE"
fi
current=$(tr -d '\n' < "$COUNTER_FILE" | tr -cd '0-9' || echo "0")
[[ -z "$current" ]] && current=0
next=$((current+1))
echo "$next" > "$COUNTER_FILE"

# ----- Calcule les stats APRES commit (mais avant de commiter, on projette) -----
if [[ "$today_has_commits" -gt 0 ]]; then
  streak_after="$streak_incl_today"
else
  streak_after=$((streak_excl_today + 1))
fi
total_after=$((total_before + 1))
now_iso=$(date -Iseconds)    # ex: 2025-09-21T10:12:34+02:00
commit_msg="chore(auto): +1 — $now_iso"

# ----- Ecrit stats.json cohérentes avec CE commit -----
mkdir -p "$(dirname "$STATS_FILE")"
cat > "$STATS_FILE" <<JSON
{
  "last_commit_iso": "$now_iso",
  "last_commit_msg": "$commit_msg",
  "streak_days": $streak_after,
  "total_commits": $total_after
}
JSON

# ----- Commit & push -----
git add "$COUNTER_FILE" "$STATS_FILE"
GIT_AUTHOR_DATE="$now_iso" GIT_COMMITTER_DATE="$now_iso" \
  git commit -m "$commit_msg"

git push origin "$BRANCH"

# Log propre
echo "[$(date -Iseconds)] OK push ($commit_msg) – total=$total_after, streak=$streak_after"
BASH

chmod +x ~/scripts/auto-commit.sh
