=== Watu ===
Contributors: prasunsen
Tags: exam, test, quiz, survey
Requires at least: 3.0
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later

Creates exams with unlimited number of questions and answers. Assigns grade after the exam or quiz is taken.

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

Create exams and quizzes and display the result immediately after the user takes the exam. You can assign grades and point levels for every grade in the exam / quiz. Then assign points to every answer to a question and Watu will figure out the grade based on the total number of points collected.

Watu for Wordpress is a light version of <a href="http://calendarscripts.info/watupro/" target="_blank">Watu PRO</a>. Check it if you want to run fully featured exams with data exports, student logins, categories etc. 

**Please go to Tools -&gt; Manage Exams to start creating exams.**

### Features ###

* Creates quizzes and exams
* Use shortcodes to embed quizzes in posts or pages
* Single-choice questions
* Multiple-choice questions
* Open-end questions (essays)
* Required questions
* Grades
* Shows answers at the end of the quiz or immediately after selection
* List of users who took exam along with their results
* Ajax-based loading of the quiz results.


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

**Please do not place more than one code in one post or page. Only one exam will be shown at a time. If you wish more exams to be displayed, please give links to them!**

== Screenshots ==

1. List of your exams with shortcodes for embedding in posts or pages
2. The form for creating and editing an exam/test
3. You can add unlimited number of questions in each exam, and each question can of single-answer, multiple-answer, or open-end type. 

== Changelog ==

= Changes in 2.2.5 =
- Animate back to top when submitting exam, and when clicking "next" after long question. This prevents confusion when user has to see the next screen.
- Fixed bug with "Question X of Y total" showing even for single-page quizzes
- Each exam / quiz has its own setting about how the answers will be shown
- As many themes started showing the choices under radio buttons or checkboxes, added explicit CSS to keep them on the same line
- Fixed bug with skipping "0" answers
- Changed %%TOTAL%% to %%MAX-POINTS%% for clarify and consistency. The old tag will keep working. 
- Further code improvements 

= Changes in 2.2 = 
- Replaced 'the_content' filter with custom filter to avoid issues with membership plugins
- Cleanup the root folder from show_exam.php
- Another method added to the API, see the new docs
- The answers field changed to TEXT so you can now add long choices/answers to the questions
- Fixed bug in the list of taken exams
- Fixed issues with correct/wrong answer calculation
- Added %%CORRECT%% answers variable to display number of correct answers
- Watu scripts and CSS are now loaded only when you have exams on the page avoiding unnecessary page overload 
- Other code fixes and improvements

= Changes in 2.1 =
- Displaying "Question X of Y" so the user knows where they are
- Fixing incompatibility with Paid Membership PRO
- Shortcodes on the final screen
- Starting API (Not yet documented)
- Code fixes and improvements

= Changes in 2.0 =
- Required questions (optional)
- A list of users who took an exam along with their results
- Localization of the strings in the javascript
- More flexible function to add new DB fields on update
- Code fixes and improvements

= Changes in 1.9 =
- Grade title and description are now separated
- Shortcodes will be executed in questions and final screen
- Code fixes and improvements
- Localization issues fixed

= Changes in 1.8 =
- the exam title links to the post with this exam if exam is already published
- "show all questions on single page" is now configurable for every exam
- Improving code continued (more to come)

= Changes in 1.7 =

- You can now randomize the questions in a quiz
- Fixed issues with the DB tables during upgrade
- Removed more obsolete code, fixed code issues. More on this to come.

= Changes in 1.6 =

- Removed obsolete rich text editor and replaced with wp_editor call
- Added "Essay" (open-end) question 
- Resolved possible Javascript conflicts
- Internationalization ready - find the .pot file in langs/ folder

== Developers API ==

In order to allow other plugins to integrate better to Watu we have started working on developers API.
The following action calls are currently available:

= do_action('watu_exam_submitted', $taking_id)  
Called when exam is submitted, passes the taken exam ID 

= do_action('watu_exam_saved', $exam_id)
Called when you add or edit exam (after submitting the changes). Passes the changed exam ID. 