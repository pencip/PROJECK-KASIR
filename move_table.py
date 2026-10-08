import re

with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'r') as f:
    content = f.read()

# 1. Extract the table block
# It starts with {{-- ═══════════════════════════════════════════════════════════════════════ --}}
# followed by {{-- Transaksi Kasir Terkini                                                --}}
# and ends with the </div> right before @endsection

pattern_table = r"(\{\{-- ═══════════════════════════════════════════════════════════════════════ --\}\}\n\{\{-- Transaksi Kasir Terkini                                                --\}\}\n\{\{-- ═══════════════════════════════════════════════════════════════════════ --\}\}\n<div class=\"tbl-card\">.*?\n</div>\n\n)"

match_table = re.search(pattern_table, content, re.DOTALL)
if not match_table:
    print("Table block not found")
    exit(1)

table_block = match_table.group(1)

# Modify table_block to remove margin-bottom if possible, or add inline style
table_block_modified = table_block.replace('<div class="tbl-card">', '<div class="tbl-card" style="margin-bottom: 0;">')

# Remove the table block from its original position
content = content.replace(table_block, '')

# 2. Wrap chart-card and insert table_block below it
# Find the start of main-section and chart-card
# <div class="main-section">
# 
#     {{-- ── LEFT: Progress Overview / Area Chart ── --}}
#     <div class="chart-card">
# ...
#     </div>
# 
#     {{-- ── RIGHT: 3 Compact Donut Cards (stacked) ── --}}

pattern_chart = r"(<div class=\"main-section\">\n\n\s*\{\{-- ── LEFT: Progress Overview \/ Area Chart ── --\}\}\n\s*<div class=\"chart-card\">.*?\n\s*</div>\n)(?=\n\s*\{\{-- ── RIGHT: 3 Compact Donut Cards \(stacked\) ── --\}\})"

match_chart = re.search(pattern_chart, content, re.DOTALL)
if not match_chart:
    print("Chart block not found")
    exit(1)

chart_block = match_chart.group(1)

# Now, we want to replace chart_block with:
# <div class="main-section">
#     <div class="left-col" style="display: flex; flex-direction: column; gap: 16px;">
#         (original chart-card part without <div class="main-section">)
#         (table_block_modified)
#     </div>
#
new_chart_block = chart_block.replace(
    '<div class="main-section">\n\n    {{-- ── LEFT: Progress Overview / Area Chart ── --}}',
    '<div class="main-section">\n\n    <div class="left-col" style="display: flex; flex-direction: column; gap: 16px;">\n        {{-- ── LEFT: Progress Overview / Area Chart ── --}}'
)
# Add the table block and close left-col div
new_chart_block += '\n' + table_block_modified + '    </div>\n'

content = content.replace(chart_block, new_chart_block)

# Remove the remaining </div>{{-- /main-section --}} spacing if needed, but it should be fine.
with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'w') as f:
    f.write(content)

print("Done")
