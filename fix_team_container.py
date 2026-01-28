import re

# Read file
with open('VietSpeak/index.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Find and replace the team-container section
pattern = r'(<div class="profile-grid" id="team-container">).*?(</div>\s*</section>)'
replacement = r'\1\n            </div>\n        \2'

new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

# Write back
with open('VietSpeak/index.html', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Fixed! Removed hardcoded content from team-container")
