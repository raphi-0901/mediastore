@echo off
git add .
set /p message=Wie lautet dein cooler Kommentar :D ? 
git commit -m "%message%"
git pull
git push 
pause
