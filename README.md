# HFSDiskImageWrapper
A little php webpage that allows you to upload a file and recieve it back wrapped in a Classic Mac OS compatible disk image. These images should work straight with the BMOW Floppy EMU or Mini vmac.

You can find a short demo of the page in use here :

https://youtu.be/OfE0ehdpdZo


From notes.txt :

Need PHP installed
Make sure there is a folder called "uploads" in the same directory with the correct permissions.
The command line tool "genisoimage" must be installed as it is used to wrap the files

To start a development test server on linux (or similar), navigate to the folder in the commandline and
type :
php -S localhost:1984
You can then use a web browser to access the test server by entering the url "localhost:1984"

Command to remove folders older than 20 minutes :
sudo find /sharedfolders/macimagefolder/uploads/ -maxdepth 1 -mmin +20 -type d -exec rm -r {} \;
