=== Plugin Name ===
Contributors: prasunsen
Tags: exam, test, quiz, survey
Requires at least: 2.0.2
Tested up to: 3.2.1
Stable tag: trunk
License: GPLv2 or later

Creates exams with unlimited number of questions and answers. Assigns grade after the exam is taken.

== Description ==

Create exams and quizzes and display the result immediately after the user takes the exam. You can assign grades and point levels for every grade. Then assign points to every answer to a question and Watu will figure out the grade based on the total number of points collected.

Watu for Wordpress is a light version of <a href="http://calendarscripts.info/online-exam-software.html" target="_blank">Watu Exam</a>. Check it if you want to run fully featured exams with data exports, student logins, categories etc. 


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