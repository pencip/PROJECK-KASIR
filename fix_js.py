import re

with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'r') as f:
    content = f.read()

pattern = r"(// ── Filter Toggle ──────────────────────────────────────────────────────────\n.*?document\.getElementById\('srchMethod'\)\.addEventListener\('change', doFilter\);)"

def repl(m):
    block = m.group(1)
    return "/*\n" + block + "\n*/"

content, count = re.subn(pattern, repl, content, flags=re.DOTALL)
print(f"Commented out JS block: {count} times")

with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'w') as f:
    f.write(content)

