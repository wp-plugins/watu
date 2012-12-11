=== Plugin Name ===
Contributors: prasunsen
Tags: exam, test, quiz, survey
Requires at least: 2.0.2
Tested up to: 3.4.2
Stable tag: trunk
License: GPLv2 or later

Creates exams with unlimited number of questions and answers. Assigns grade after the exam is taken.

== License ==

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

== Description ==

Create exams and quizzes and display the result immediately after the user takes the exam. You can assign grades and point levels for every grade. Then assign points to every answer to a question and Watu will figure out the grade based on the total number of points collected.

Watu for Wordpress is a light Wordpress version of <a href="http://calendarscripts.info/watupro/" target="_blank">Watu PRO</a>. Check it if you want to run fully featured exams with data exports, student logins, categories etc. 

<b>Please go to Tools -&gt; Manage Exams to start creating exams.</b>

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire folder `watu` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to "Watu Settings" to change the default settings (optional)
1. Go to "Manage Exams" under "Tools" menu to create your exams, add questions, answers and grades. On the "manage questions" page of the created exam page, above the questions table you will see a green text. It shows you the code you need to enter in a post content where you want the exam to appear.

== Frequently Asked Questions ==

= How are grades calculated? =

Watu computes the number of points in total collected by the answers given by the visitor. Then it finds the grade. For example: If you have 2 questions and the correct answers in them give 5 points each, the visitor will collect either 0, or 5 or 10 points at the end. You may decide to define grades "Failed" for 0 to 4 points and "Passed" for those who collected more than 4 points. In reality you are going to have more questions and answers and some answers may be partly correct which gives you full flexibility in assigning points and managing the grades.

= Can I assign negative points? =

Yes. It's even highly recommended for answers to questions that allow multuple answers. If you just assign 0 points to the wrong answers in such question the visitor could check all the checkboxes and collect all the points to that question.

= How do I show the exam to the visitors of my blog? =

You need to create a post and embed the exam code. The exam code is shown in the green text above the questions table in "Manage questions" page for that exam.

<strong>Please do not place more than one code in one post or page. Only one exam will be shown at a time. If you wish more exams to be displayed, please give links to them!</strong>

== Changelog ==

Please note change log started being recorded after version 1.5.

= Changes in 1.7 =

- You can now randomize the questions in a quiz
- Fixed issues with the DB tables during upgrade
- Removed more obsolete code, fixed code issues. More on this to come.

= Changes in 1.6 =

- Removed obsolete rich text editor and replaced with wp_editor call
- Added "Essay" (open-end) question 
- Resolved possible Javascript conflicts
- Internationalization ready - find the .pot file in langs/ folder