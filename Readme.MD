# The App

This project was a personal challenge written in one sitting over a weekend. I wrote this single-page app to help my wife study the present tense in French. The website is for a single user.

The app displays a number of randomly picked French sentences. The user must fill in the blank with the verb given in parentheses and conjugate it correctly.

The default number of sentences shown on the page (by default, 5) is customizable by the user. 

When the user submits their answers, they are compared with the right answers. If the user's answer is correct, their input is displayed in green. Otherwise, the sentence with the correct answer is displayed below their input so that the user can see their mistake.

The user's score is saved in a text file. The sentences are pulled from a selection of sentences in another text file

Once the user has seen how they did, they can choose to try a new set of sentences to continue practicing their French conjugation skills.

# The Stack

This app was built with plain HTML, Bootstrap, jQuery, and php.

# Details On The Flow Of The App

The webpage contains two forms that POST the data to itself. The page has three "states":

1. If $_POST is empty, the page will parse a file called sentences.txt into an array. Unique pseudo-random numbers between zero and the size of the array are generated to pick sentences in the array. For each sentence, the hardcoded *&lt;missing&gt;* HTML tag is replaced by an input field and an open and close bold tag is added around the verb in parentheses.

If $_POST is set, the page examines which of the two forms was submitted and act accordingly:

2. If the user submitted the configuration form, the page will pick a new set of sentences and adjust the number of sentences that are displayed at once depending on the user's input (a number between 1 or 10). Note: the number of displayed sentences can also be modified with $_GET (with a *?selected_number_of_sentences=XX* parameter in the URL) if $_POST is not set.

3. If the user submitted the exercise form, then the page will display the answers. It will also parse, update, and write two counters (in *right.txt* and *total.txt* in the */exercise_assets* folder) and display these updated figures.

$_SESSION is also used to keep track of the number of times a user submitted the exercise form. By setting and checking $_SESSION, the page prevents the script from adding to the counters when the user resubmit the form with the same content (through a page refresh, for example) instead of submitting new data.

To reinitialize the counter, simply remove the numbers in *right.txt* and *total.txt*.

# Improvements

I do not plan on continuing the development of this app. However, I know that it could benefit from the following enhancement:

* Secure the input coming from $_POST, $_GET, and $_SESSION to prevent malicious use of the from.

* Create an add.php page so that a user can submit new sentences to add to sentences.txt.

* Create a stat.php page so that the user can see (and reset) their score without submitting the exercise form.

* Keep track of the last time the user logged in and display a welcome back message when $_POST and $_SESSION are not set.

* Send a reminder email if the user has not been doing exercises for more than 7 days -- with an option to deactivate automated emails.

* Add multi-user support.

* Create different flavor of this exercise with other tenses such as *imparfait* or the future tense.