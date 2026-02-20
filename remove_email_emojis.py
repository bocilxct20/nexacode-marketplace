import re
import os
from pathlib import Path

# Emoji pattern
emoji_pattern = re.compile(r'[🚀🎉✨💎📦🔥🌟💡📅🔑📑⚡🎯💰🏆👋🙏😊👍✅❌⚠️🔔📧📬🎁🛒💳🔐💸🥳💬🎫⏰⭐📝]+\s*')

# Directory containing mail classes
mail_dir = Path(r'C:\Users\dani\Documents\nexacode\marketplace\app\Mail')

files_modified = []

for php_file in mail_dir.glob('*.php'):
    content = php_file.read_text(encoding='utf-8')
    original_content = content
    
    # Remove emojis from subject lines
    content = re.sub(r"(subject:\s*')([🚀🎉✨💎📦🔥🌟💡📅🔑📑⚡🎯💰🏆👋🙏😊👍✅❌⚠️🔔📧📬🎁🛒💳🔐💸🥳💬🎫⏰⭐📝]+\s*)", r'\1', content)
    
    if content != original_content:
        php_file.write_text(content, encoding='utf-8')
        files_modified.append(php_file.name)
        print(f"✓ Fixed: {php_file.name}")

print(f"\n✅ Total files modified: {len(files_modified)}")
print("Files:", ', '.join(files_modified))
