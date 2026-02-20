
import sys
import re

def audit_flux_tags(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    components = [
        'flux:modal', 'flux:card', 'flux:field', 'flux:heading', 'flux:subheading',
        'flux:button', 'flux:input', 'flux:textarea', 'flux:badge', 'flux:select',
        'flux:separator', 'flux:spacer', 'flux:dropdown', 'flux:menu', 'flux:navbar',
        'flux:brand', 'flux:composer'
    ]
    
    results = {}
    for comp in components:
        open_tag = len(re.findall(rf'<{comp}(?![a-z:])', content))
        close_tag = len(re.findall(rf'</{comp}>', content))
        # Check for self-closing tags like <flux:icon ... /> but icons are separate
        # Some components might be self-closed
        self_closed = len(re.findall(rf'<{comp}[^>]*/>', content))
        results[comp] = {'open': open_tag, 'close': close_tag, 'self_closed': self_closed}
    
    print(f"{'Component':<20} | {'Open':<5} | {'Close':<5} | {'Self':<5} | {'Balance':<7}")
    print("-" * 55)
    for comp, counts in results.items():
        balance = counts['open'] - counts['close'] - counts['self_closed']
        print(f"{comp:<20} | {counts['open']:<5} | {counts['close']:<5} | {counts['self_closed']:<5} | {balance:<7}")

if __name__ == "__main__":
    audit_flux_tags(sys.argv[1])
