<?php
    // default number of sentences to display (may be modified; invalid values will be replaced by a default value)
    $selected_number_of_sentences = 5;
    // init core variables
    $number_of_sentences = 10;
    if (!isset($selected_number_of_sentences) || !($selected_number_of_sentences > 0)) $selected_number_of_sentences = 10;
    $status = "question";
    // values modified on submit
    if (isset($_POST["number-of-sentences"])) $selected_number_of_sentences = $_POST["number-of-sentences"];
    else if (isset($_GET["selected_number_of_sentences"])) $selected_number_of_sentences = $_GET["selected_number_of_sentences"];
    if (isset($_POST["form-id"])) if ($_POST["form-id"] == "exercise") $status = "answer";

    // session
    if (!isset($_SESSION)) session_start();
    // to prevent form re-submission
    if ($status == "answer") {
        if (!isset($_SESSION['session-id'])) $_SESSION['session-id'] = 1;
        else $_SESSION['session-id'] = $_SESSION['session-id'] + 1;
    }
    else unset($_SESSION['session-id']);

    // select field rendered
    function SelectNumberofSentences($max_sentences,$selected_number) {
        // hardcoded options (in french) for the selector
        $french_numbers = ["zero","une","deux","trois","quatre","cinq","six","sept","huit","neuf","dix"];
?>
            <form id=config action="" method=post>
                <label for=number-of-sentences>Choisissez le nombre de phrases à afficher:</label>
                <select name=number-of-sentences class="input-lg">
<?php
        // if the number of sentences to display is higher than the number of options, then adjust the global
        if ($selected_number > sizeof($french_numbers)) {
            $selected_number = sizeof($french_numbers) - 1;
            $GLOBALS['selected_number_of_sentences'] = sizeof($french_numbers) - 1;
        }
        // loop to create each option
        for ($i = 1; $i <= $max_sentences; $i++) {
            // when you find the desired option, select it by default
            if ($i == $selected_number) $selected = " selected";
            else $selected = "";
?>
                 <option<?=$selected?> value=<?=$i?>><?=$french_numbers[$i]?></option>
<?php   } ?>
                </select>
                <button type=submit class="btn btn-lg btn-success">Changer</button>
                <input name=form-id value=config readonly hidden>
            </form>
<?php
    }

    // display sentences
    function DisplaySentences($selected_number_of_sentences,$status) {
        // fetch sentences from a text file
        $sentences = ParseSentenceFile($selected_number_of_sentences,$status);
        // if there is not enough sentences in the sentence text file, adjust the number of sentences to display and refetch sentences
        if (sizeof($sentences) < $selected_number_of_sentences) {
            $selected_number_of_sentences = sizeof($sentences) - 1;
            $GLOBALS['selected_number_of_sentences'] = $selected_number_of_sentences;
            $sentences = ParseSentenceFile($selected_number_of_sentences,$status);
        }
?>
            <form id=exercise method=post>
                <ol>
<?php
        // loop each sentence
        for ($i = 1; $i <= $selected_number_of_sentences; $i++) {
            // correction mode
            if ($status == "answer") {
                $correction = "wrong";
                // parse answer file
                $answer_file = file_get_contents('exercise_assets/answers.txt');
                $answers = explode("\n", $answer_file);
                array_unshift($answers,"empty");
                // compare user's answer with the answer in the file
                if ($_POST['answer' . $i] == $answers[$_POST['sentence' . $i]]) $correction = "right";
                // show the right answer below the user's answer if they got it wrong
                $answer[$i] = "";
                if ($correction == "wrong") {
                    $answer[$i] = "<br><i>" . str_replace(
                        "<missing>",
                        "<span class='answer'>" . $answers[$_POST['sentence' . $i]] . "</span>",
                        $sentences[$i]
                    ) . "</i>";
                }
                // catch the user's input
                $user_answer = $_POST['answer' . $i];
                // if the user's input is empty, display a message
                if (empty($user_answer)) $user_answer = "[vide]";
                // replace the missing word with the user's input
                $sentences[$i] = str_replace(
                    "<missing>",
                    "<input class='input-lg exercise-field " . $correction . "' value='" .  htmlentities($user_answer,ENT_QUOTES) . "' readonly>",
                    $sentences[$i]
                );
            }
            // question mode
            else {
                // replace the missing word with an input field
                $sentences[$i] = str_replace(
                    "<missing>",
                    "<input name='answer" . $i . "' class='input-lg exercise-field'>",
                    $sentences[$i]
                    );
                $answer[$i] = "";
            }
            // both for correction and question mode
            // add an open bold tag to the keyword
            $sentences[$i] = str_replace(
                "(",
                "<b>(",
                $sentences[$i]
            );
            // add a close bold tag to the keyword
            $sentences[$i] = str_replace(
                ")",
                ")</b>",
                $sentences[$i]
            );
?>
                    <li><?=$sentences[$i]?><?=$answer[$i]?></li>
<?php
        }; 
?>
                </ol>
<?php
// show a different button depending on correction/question mode
SubmitToggle($status,$selected_number_of_sentences)
?>
                <input name=form-id value=exercise readonly hidden>
                <input name=number-of-sentences value=<?=$selected_number_of_sentences?> readonly hidden>
            </form>
<?php
    }

    // parse the file that contains the sentences of the exercise
    function ParseSentenceFile($number_of_sentences,$status) {
        // parse the sentence file in an array
        $sentence_file = file_get_contents('exercise_assets/sentences.txt');
        $sentences = explode("\n", $sentence_file);
        array_unshift($sentences,"empty");
        $random_sentence_number[0] = "empty";
        // adjust to the number of sentences in the files if there is not enough compared of the number of sentences requested
        if ($number_of_sentences > sizeof($sentences) - 1) $number_of_sentences = sizeof($sentences) - 1;
        // loop the sentences
        for ($i = 1; $i <= $number_of_sentences; $i++) {
            // in correction mode, fetch the sentences that were previously selected
            if ($status == "answer") {
                $selected_sentences[$i] = $sentences[$_POST['sentence' . $i]];
            }
            else {
                // in question mode, makes sure that the same sentence does not come twice
                do {
                    $random_number = rand(1,sizeof($sentences) - 1);
                } while (in_array($random_number,$random_sentence_number));
                // save the random number in an array
                $random_sentence_number[$i] = $random_number;
                // add a hidden field to identify the sentence
                $selected_sentences[$i] = $sentences[$random_sentence_number[$i]] . "<input name='sentence" . $i . "' value=" . $random_sentence_number[$i] . " readonly hidden>";
            }
        }
        return $selected_sentences;
    }

    // show a different button depending on the mode (correction/question)
    function SubmitToggle($status,$selected_number_of_sentences) {
        if ($status == "answer") {
?>
                <a href="?selected_number_of_sentences=<?=$selected_number_of_sentences?>" class="btn btn-primary btn-lg btn-block">Faire une autre série de verbes</a>
<?php
        }
        else {
?>
                <button class="btn btn-primary btn-lg btn-block">Voir la correction</button>
<?php
        }
    }

    // display stats
    function Stats($number_of_sentences,$status) {
        if ($status == "answer") {
            // parse answer file
            $answer_file = file_get_contents('exercise_assets/answers.txt');
            $answers = explode("\n", $answer_file);
            array_unshift($answers,"empty");
            $right_answers = 0;
            for ($i = 1; $i <= $number_of_sentences; $i++) {
                if ($_POST['answer' . $i] == $answers[$_POST['sentence' . $i]]) $right_answers++;
            }
            // parse stat file
            $right_file = file_get_contents('exercise_assets/right.txt');
            $total_file = file_get_contents('exercise_assets/total.txt');
            // update stat file (and prevents incrementing stats if form is being resubmitted)
            if ($_SESSION['session-id'] == 1) {
                $updated_right = $right_file + $right_answers;
                file_put_contents('exercise_assets/right.txt', $updated_right);
                $updated_total = $total_file + $number_of_sentences;
                file_put_contents('exercise_assets/total.txt', $updated_total);
            }
            else {
                $updated_right = $right_file;
                $updated_total = $total_file;
            }
            // avoid dividing by zero
            if ($updated_right != 0) $percent = " (" . ceil($updated_right/$updated_total*100) . "%)";
            else $percent = "";

?>
            <p class="right lead"><big>Vous avez <?=$right_answers?> réponses justes.</big></p>
            <p class="right lead"><big>En tout, vous avez répondu juste <?=$updated_right?> fois sur <?=$updated_total?><?=$percent?>.</big></p>
<?php
        }
    }
/* top of the page */
?>
<!doctype>
<html>
    <head>
        <title>Exercice de français: Le présent</title>
        <meta charset=utf-8>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- custom styling -->
        <style>
        h1 {text-align: center;}
        @media(max-width:768px){h2{text-align: center;}}
        label {font-size: 1.75rem;}
        li {padding: 0.5rem;line-height: 3rem; font-size: 2rem;}
        .exercise-field {background: rgb(245,245,245); width: 15rem;}
        .wrong {color: red;font-weight: bold;}
        .right {color: green;font-weight: bold;}
        .answer {color: blue;font-weight: bold;}
        </style>
    </head>
    <body lang=fr-FR>
        <div class=container>
            <!-- titling -->
            <h1>Exercice de français: Le&nbsp;présent</h1>
            <!-- assignment -->
            <h2>Conjuguez les verbes entre parenthèses au présent de l'indicatif</h2>
            <!-- select the number of sentences to display -->
<?php SelectNumberofSentences($number_of_sentences,$selected_number_of_sentences) ?>
<?php Stats($selected_number_of_sentences,$status) ?>
            <!-- exercise -->
<?php DisplaySentences($selected_number_of_sentences,$status) ?>
        </div>
        <!-- debug javascript -->
        <script>
            console.log('number_of_sentences = <?=json_decode($number_of_sentences, JSON_NUMERIC_CHECK)?>')
            console.log('selected_number_of_sentences = <?=json_encode($selected_number_of_sentences, JSON_NUMERIC_CHECK)?>')
            console.log('status = <?=json_encode($status, JSON_NUMERIC_CHECK)?>')
        </script>
    </body>
</html>
<?php
    $_SESSION['init'] = false;
?>