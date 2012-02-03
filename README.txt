=== Code to Widget ===
Tags: code, php, widget
Contributors: seanklein
Requires at least: 3.0
Tested up to: 3.4
Stable tag: 1.2

Code to Widget Plugin uses PHP files from a specified directory, and (if the file has the proper template tags) adds a Widget.

== Description ==

Code to Widget Plugin uses PHP files from a specified directory, and (if the file has the proper template tags) adds a Widget.

== Details ==

Code to Widget provides an easy way to create widgets, without having to deal with writing the widget class everytime

== Installation ==

1. Download the plugin archive and expand it.
2. Put the code-to-widget directory into your wp-content/plugins/ directory
3. Go to the Plugins page in your WordPress Administration area and click 'Activate' for Code to Widget.
4. Go to the Code to Widget Options page (Settings > Code to Widget) to set the directory for the plugin to search for widgets.  The directory to search for widgets must be an absolute path.  There are some helpful paths included on the Settings page.

== Configuration ==

The plugin provides an interface for selecting a specific folder to search for widgets in.  The folder path entered into the settings page must be an absolute path.  Then the widgets are easily configurable similar to all other widgets.

The files in the specified folder need to have specific template tags to be added as widgets.  The following code will need to be added to the top of the files:

	<?php
	/*
	Widget Name: Test Widget 1
	Widget Description: Test 1 widget
	*/
	?>

A "Widget Title:" field can also be added. That title will be set as the default widget title when the file is selected on the widget settings screen.

== Frequently Asked Questions ==

= Why use this plugin? =

This plugin adds an easy to use/modify way to create plugins. The only thing a blog owner needs is the ability to add files to a folder and change the content of those files.  By adding the template tags to the files the content of the file will be created as a widget.

= How easy is it to create widgets? =

All that is needed to create a widget is to create a PHP file in a specified folder and add the template tags to that file.  Then all that is needed is to go to the widget settings page and add the widget to a desired sidebar, and select the file name from the drop down. Then save the widget.

== Changelog ==

= 1.2 =

* NOTE: Maintenance update
* General Code improvements
* Cleanup of some code
* Extracting as much code into views as possible

= 1.1.4 =

* Fixing an issue with the Folder check and the smiley face

= 1.1.3 =

* Fixing a bug in a constant

= 1.1.2 =

* Updating compatibility version

= 1.1.1 =

* Fixing the version number

= 1.1 =

* Added some directory information so it is easier to find the absolute path to the folder to get widgets from
* Added a "Test Directory" button to check and see if the directory entered is usable for the plugin

= 1.0 =

* Initial Release




