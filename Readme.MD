# Bonjour !

## The app

I wrote this micro-site to help my wife study the present tense in French. The app is meant to be used by one user only. The score of the user can be resetted manually.

This project was a personal challenge written in one sitting in HTML and plain PHP. The CSS layout is powered by Bootstrap/jQuery with minor custom styling.

The home of the app can be found at */present/index.php*.

## How to use this app

The app displays a number of randomly picked sentences. The user must fill in the blank with the verb given in parentheses and conjugate it correctly.

The default number of sentences to show at the same time (by default, 10) can be fined-tuned by the user. 

When the user submits his answers, they are compared with the right answers. If the user's answer is correct, their input is displayed in green; otherwise, the sentence with the correct answer is displayed below their input so that the user can see their mistake.

The number of right answers is saved as well as the number of sentences that were displayed to keep track of the user's score.

The user can click a link to see a new set of sentences and continue practicing their French conjugation skills.

## Flow of the app

Technically, this project works around a single page, index.php. The webpage contains two forms that POST the data back to the same page as well as functions: SelectNumberofSentences(), DisplaySentences(), ParseSentenceFile(), SubmitToggle(), and Stats().

There are three "states" for the index page:

1. If $_POST is empty, the page will parse a file called sentences.txt into an array. Unique pseudo-random numbers between zero and the size of the array are generated to pick sentences in the array. For each sentence, the hardcoded <missing> tag is replaced by an input field and an open and close <b> tag is added around the verb in parentheses.

If $_POST is set, the page examines which of the two forms was submitted and act accordingly:

2. If the user submitted the configuration form, the page will pick a new set of sentences and adjust the number of sentences that are displayed at once depending on the user's input (between 1 or 10). Note: the number of displayed sentences can also be displayed with $_GET (with ?selected_number_of_sentences=XX) if $_POST is not set.

3. If the user submitted the exercise form, then the page will display the answers. It will also parse, update, and write two counters (in right.txt and total.txt in the /exercise_assets folder) and display these updated figures.

$_SESSION is also used to keep track of the number of times a user submitted the exercise form. By setting and checking $_SESSION, the page prevents the script from adding to the counters when the user resubmit the form with the same content (through a page refresh, for example) instead of submitting new data.

To reinitialize the counter, simply remove the numbers in right.txt and total.txt.

## Other ideas that could be worth implement to the app

* Make the processing of $_POST, $_GET, and $_SESSION more secure to prevent malicious use of the variable and submission from foreign pages.

* Create an add.php page so that a user can submit new sentences to add to sentences.txt.

* Create a stat.php page so that the user can see (and reset) their score without submitting the exercise form.

* Keep track of the last time the user logged in and display a welcome back message when $_POST and $_SESSION are not set.

* Send a reminder email if the user has not been doing exercises for more than 7 days (or any other arbitrary number) with an option to deactivate automated emails.

* Add multi-user support (would there be a way to avoid login?).

* Rewrite the code in React.

* Create different flavor of this exercise with other tenses such as *imparfait* or the future tense.