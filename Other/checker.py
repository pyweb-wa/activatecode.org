import os
import time

# Path to directory to monitor
directory_to_monitor  = '/var/www/html/sms-platform'

# Path to file to save PHP file names
php_filepath_file  = '/home/pyweb/monitorphp/html_smsplatformPHP.txt'

# Path to file to save paths of new PHP files
new_php_filepath_file = '/home/pyweb/monitorphp/newphpfile.txt'


# Get initial list of PHP file paths
php_files = []
for root, dirs, files in os.walk(directory_to_monitor):
    for file in files:
        if file.endswith('.php'):
            php_files.append(os.path.join(root, file))

# Write PHP file paths to file
with open(php_filepath_file, 'w') as f:
    for filepath in php_files:
        f.write(filepath + '\n')

# Monitor directory for new PHP files
while True:
    time.sleep(20)  # Wait for 1 minute

    # Get current list of PHP file paths
    current_php_files = []
    for root, dirs, files in os.walk(directory_to_monitor):
        for file in files:
            if file.endswith('.php'):
                current_php_files.append(os.path.join(root, file))

    # Check for new PHP files
    new_php_files = []
    for filepath in current_php_files:
        if filepath not in php_files:
            new_php_files.append(filepath)

            # Rename file and remove .php extension
            new_filepath = os.path.splitext(filepath)[0]  # Remove .php extension
            print(f"new file was added {filepath}")
            os.rename(filepath, new_filepath)

            # Update list of PHP file paths
            php_files.append(new_filepath)

            # Write PHP file paths to file
            with open(php_filepath_file, 'a') as f:
                f.write(new_filepath + '\n')

    # Write paths of new PHP files to file
    if new_php_files:
        with open(new_php_filepath_file, 'a') as f:
            for filepath in new_php_files:
                f.write(filepath + '\n')