# Quick Start Guide: Laragon Setup

## 📥 Download Laragon

https://laragon.org/download/ (Choose **Laragon Full**)

## 🚀 Auto Setup (Recommended)

**Option 1: Batch Script** (Simple)

1. Right-click `setup-laragon.bat`
2. Select "Run as Administrator"
3. Follow prompts

**Option 2: PowerShell** (Advanced)

1. Right-click `setup-laragon.ps1`
2. Select "Run with PowerShell" (as Admin)
3. If blocked, run: `Set-ExecutionPolicy Bypass -Scope Process`

## ✅ What the script does:

✔️ Checks Laragon installation  
✔️ Copies projects to `C:\laragon\www\`  
✔️ Creates Virtual Hosts automatically  
✔️ Updates `config.js` API URLs  
✔️ Adds domains to hosts file  
✔️ Runs `composer install` & migrations  
✔️ Starts Laragon

## 🌐 Access After Setup:

- **Laravel Backend:** http://presentation-management.test
- **VietSpeak Frontend:** http://vietspeak.test

## 🔧 Manual Start:

1. Open Laragon from system tray
2. Click "Start All"
3. Access URLs above

---

**Bonus:** With Laragon, AI processing won't block other requests anymore! 🎉
