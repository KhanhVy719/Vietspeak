#!/bin/bash

echo "=========================================="
echo "🔍 TEAM MEMBERS DEBUG SCRIPT"
echo "=========================================="
echo ""

echo "1️⃣ Checking files on VPS..."
echo "-------------------------------------------"
echo "📄 team.js exists?"
ls -lh VietSpeak/team.js
echo ""

echo "📄 First 10 lines of team.js:"
head -10 VietSpeak/team.js
echo ""

echo "📄 API_BASE_URL defined?"
grep -n "API_BASE_URL" VietSpeak/team.js
echo ""

echo "📄 index.html has team-container?"
grep -n "team-container" VietSpeak/index.html
echo ""

echo "📄 index.html has team.js script?"
grep -n "team.js" VietSpeak/index.html
echo ""

echo "2️⃣ Testing API Endpoint..."
echo "-------------------------------------------"
echo "🌐 Calling: https://vietspeakai.tranhungdaocfs.site/api/public/team"
curl -s https://vietspeakai.tranhungdaocfs.site/api/public/team | jq '.' 2>/dev/null || curl -s https://vietspeakai.tranhungdaocfs.site/api/public/team
echo ""
echo ""

echo "3️⃣ Testing Frontend Access..."
echo "-------------------------------------------"
echo "🌐 Checking if team.js is accessible:"
curl -I https://khanhwiee.site/team.js 2>&1 | head -5
echo ""

echo "4️⃣ Docker Status..."
echo "-------------------------------------------"
docker ps | grep -E "laravel_app|web_server"
echo ""

echo "5️⃣ Nginx Logs (last 10 lines)..."
echo "-------------------------------------------"
docker logs web_server --tail=10 2>&1 | grep -i team || echo "No team-related logs found"
echo ""

echo "6️⃣ Laravel Logs (last 10 lines)..."
echo "-------------------------------------------"
docker logs laravel_app --tail=10 2>&1 | grep -i team || echo "No team-related logs found"
echo ""

echo "=========================================="
echo "✅ DEBUG COMPLETE"
echo "=========================================="
echo ""
echo "📋 Quick Fixes:"
echo "  - If API returns error → Check Laravel logs"
echo "  - If team.js 404 → File not in correct location"
echo "  - If CORS error → Check browser console"
echo "  - If API_BASE_URL missing → Re-pull from git"
echo ""
