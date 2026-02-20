
import sys

def count_tags(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Simple count of <div> and </div>
    # Note: This is naive and doesn't account for flux: components, but usually
    # standard divs are the ones that cause root closure issues.
    
    open_divs = content.count('<div ') + content.count('<div>')
    close_divs = content.count('</div>')
    
    print(f"Total <div: {open_divs}")
    print(f"Total </div>: {close_divs}")
    
    # Line by line tracking
    stack = 0
    lines = content.split('\n')
    for i, line in enumerate(lines):
        o = line.count('<div ') + line.count('<div>')
        c = line.count('</div>')
        stack += o
        stack -= c
        if stack < 0:
            print(f"ERROR: Stack went negative at line {i+1}: {line.strip()}")
            stack = 0 # Reset to keep searching
    
    print(f"Final Stack: {stack}")

if __name__ == "__main__":
    count_tags(sys.argv[1])
