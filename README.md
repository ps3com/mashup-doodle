mashup-doodle
=============

A mashup type course format for moodle which allows a teacher to define pages of aggregated widgets in various column/row layouts and share these with students.  These pages can also be imported/exported in <a href="http://omdl.org/">OMDL format</a>. 

![Alt text](screenshot.jpg "Mashup screenshot")

At present the mashup format supports W3C widgets using Apache Wookie and Open Social gadgets using Apache Shindig.

Prequisites
-----------

	You will need to install Apache Tomcat (http://tomcat.apache.org/) or another servlet container in order 
	to run both Apache Wookie (http://wookie.apache.org/) and Apache Shindig (http://shindig.apache.org/).
	A web archive (WAR) is available at the above locations for both Wookie and Shindig.
	
	Additionally, you will also have to enable proxypass directives in http.conf or some other method of
	relaying requests from moodle to your tomcat running webapps.
	
	The following examples are taken from an apache server http.conf file and shows which contexts are needed
	to be defined... (Note this will differ slightly when using vhosts)
	
	ProxyPass /wookie http://localhost:8080/wookie
	ProxyPassReverse /wookie http://localhost:8080/wookie

	ProxyPass /gadgets http://localhost:8080/gadgets
	ProxyPassReverse /gadgets http://localhost:8080/gadgets

	ProxyPass /rpc http://localhost:8080/rpc
	ProxyPassReverse /rpc http://localhost:8080/rpc
	
	...replacing localhost with your public IP or Domain name

Getting Started
---------------

(1) Download and install Moodle (http://download.moodle.org/)
	
	If you are not using one of Moodles "all-in-one" packages you will have to install Apache Server first and
	then configure your server to run Moodle.  See the moodle site above for more information.
	
	(Note: This codebase is being primarily developed against Moodle 2.4 on Windows using the XAMPP package)
	
(2) Create a new folder called "mashup" under path-to-moodle/course/format/

	i.e. Create "path-to-moodle/course/format/mashup"
	
(3) Check out this project and put everything in the folder you just created

(4) Start your tomcat server with your deployed instances of Wookie and Shindig

(6) Startup Moodle

	Login as an administrator and go to the notification page.  Moodle should have found the new course format 
	follow the on screen instructions to install the new format.

(7) Next go to the Course format administration page

	Settings->Site Administration->Plugins->Course Formats->Mashup Format
	
	Here you can specify where your Shindig and Wookie servers can be found, as well as some other Wookie 
	specific settings (Use the defaults for Wookie API key, admin username & password unless you have changed
	these in your wookie installation)	 
	
(8) Create a new course

	If you haven't already, as the admin user create a new user with course creator privillages
	As a course creator/teacher create a new course. Choose the "mashup" course format in the setup page.