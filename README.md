# lahwa

Habrausers activity visualizer

## Prerequisites

1. lahwa is written in PHP 7.3.9 (CLI interface) and generally with PHP7 features in mind
2. lahwa uses MySQL backend to store data; it was tested with mysql-5.7.27-r1
3. lahwa requires installed Russian locale

Here is how you install Russian locale in Gentoo Linux, other distros may go similarly.
1. Open this file for editing
`/etc/locale.gen`
2. Add this line to it or make sure it is already there
`ru_RU.UTF-8 UTF-8`
3. Save & exit the editor
4. Run
`/usr/sbin/locale-gen`
5. After it finishes, run
`locale -a`
6. If you see `ru_RU.utf8` in the output, the Russian locale is installed.

## Installation

Make a local copy of this repository; typically it involves this command

`git clone https://github.com/UrsusArctos/lahwa.git`

Copy the contents of the `conf_mysql.php.template` file into a new file called `conf_mysql.php` and adjust MySQL access credentials according to your MySQL setup.

Under `sql` subdirectory of the project, find `lw_schema.sql` file and run it through your favorite MySQL manager to create all tables in the database (which is supposed to be already existing at that point).

## Usage

To accumulate statistics of the user with HabraUser nickname, the `lw_scan.php` is used in this way:

`php -f lw_scan.php HabraUser`

Pay attention to the numeric ID it assigns to that user at the very beginning! Let's say it was 1.

To generate fancy temporal plot from the accumulated statistics, the `lw_plot.php` is used in this way:

`php -f lw_plot.php 1`

Where `1` is a numeric ID assigned to the subject by scanning tool as mentioned above. If you forget it, inspect `lw_subjects` table contents in the database.

The generated plot will be saved into the file `lwgraph-1.png` where 1 is substituted for subject ID.

Enjoy!
