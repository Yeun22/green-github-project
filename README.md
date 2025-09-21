# 🌱 Green Graph – GitHub All Green Automator

## 🚀 Purpose

This project automates daily commits (several times a day) to a GitHub repository to keep your **GitHub contribution graph all green** 🌿.

- **3 commits/day** (configurable via cron)  
- Updates a counter file (`data/counter.txt`)  
- Updates a JSON file (`public/stats.json`)  
- Small **PHP front-end** (`public/index.php`) that displays:  
  - 📅 Date and time of the last commit  
  - 🔥 Current streak (consecutive commit days)  
  - 📊 Total number of commits  

---

## 📂 Project structure

.
├── data/
│ └── counter.txt # incremented on every commit
├── public/
│ ├── index.php # small dashboard
│ └── stats.json # stats automatically updated
└── scripts/
└── auto-commit.sh # commit/push automation script


---

## ⚙️ Installation (o2switch or any Linux server)

### 1. Clone the repository

Connect to your server via SSH and clone the repo:

```bash
cd ~/www
git clone git@github.com:USER/REPO.git green-graph
cd green-graph
```
⚠️ Replace USER/REPO with your actual GitHub repository.

2. Configure Git

Inside the cloned repo:

git config user.name  "Your Name (o2switch)"
git config user.email "your.email+o2switch@example.com"

3. Add an SSH key for GitHub

On your server:

ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519_github -C "o2switch-github" -N ""


Add the public key to GitHub:

Recommended: Repo → Settings → Deploy keys → Add deploy key → check Allow write access

Or: Profile → Settings → SSH and GPG keys

Test the connection:

ssh -T git@github.com


You should see: Hi USER! You've successfully authenticated...

4. Test the script

The script is already in scripts/auto-commit.sh.
Make it executable:

chmod +x scripts/auto-commit.sh


Run it manually:

./scripts/auto-commit.sh


It should:

Increment data/counter.txt

Update public/stats.json

Create a commit and push with a message like chore(auto): +1 — 2025-09-21T10:12:34+02:00

5. Schedule with cron

Edit your crontab:

crontab -e


Example (3 commits/day at different times):

PATH=/usr/local/bin:/usr/bin:/bin
TZ=Europe/Paris
MAILTO=""

47 8  * * * /bin/bash $HOME/www/green-graph/scripts/auto-commit.sh >> $HOME/logs/auto-commit.log 2>&1
52 13 * * * /bin/bash $HOME/www/green-graph/scripts/auto-commit.sh >> $HOME/logs/auto-commit.log 2>&1
38 19 * * * /bin/bash $HOME/www/green-graph/scripts/auto-commit.sh >> $HOME/logs/auto-commit.log 2>&1

6. Access the dashboard

The front-end is available at:

https://your-domain/green-graph/public/


It will show the live commit stats 🎉

🔍 Troubleshooting

Permission denied (publickey)
→ Make sure the SSH key is correctly added to GitHub.

Cron not running
→ Check logs in ~/logs/auto-commit.log
→ Ensure PATH and TZ are set in crontab.

Host key verification failed
→ Add GitHub to known_hosts:

ssh-keyscan -H github.com >> ~/.ssh/known_hosts

🛠️ Customization

Change commit frequency → edit crontab.

Change commit message → edit scripts/auto-commit.sh.

Extend dashboard → edit public/index.php.

📜 License

Free to use for fun and to keep your GitHub graph green 🌱