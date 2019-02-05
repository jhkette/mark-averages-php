<!--Joseph Ketterer
Jkette01
Web Programming with PHP
Tobi Brodie -->

<?php
#Web Programming using PHP (P1) - TMA Functions file to be included in TMA web pages

/* I've created 2 functions that check that the title and data arrays contain
the correct amount of values. I've explained in my pseudocode that I think it is easiest
to check for these values in the beginning. Otherwise you have to deal with the possibilty of
calling values that don't exist throughout programme.

I have made an if statement on the index page which closes the file (with message) if these errors are picked up by functions below.
This is how I interpreted the specifications */

function checkTitle($handle){

   $error = 0;
   $title = fgets($handle);
   $title = htmlentities(trim($title)); #ensure text is converted to HTML chars
   $title = explode( ',' , $title );
   if (count($title) !== 4 ){ #if there are not 4 items in array report error
      $error++;
   }
   return $error; #this value goes to if statement on index page
}

/* If I don't check if it isn't empty sometimes a blank line at the end of file can
get read as an error. I use '!empty' to account for this and just record actual errors. */
function checkData($handle){

fgets($handle);
$error = 0; # declare error variable
while (!feof($handle)) {
    $line = fgets($handle);
    $line = htmlentities(trim($line)); #ensure text is converted to HTML chars
    $count = explode( ',' , $line );
    if (!empty($line) && (count($count) !== 2))#if there are not 2 items report error(if not an empty line)
    {
        $error++; # if there are not 2 items in array report error
        }
    }
    return $error; # this value goes to if statement on index page
}


/* Function to get the title data array which then gets validated by other functions. */
function getTitle($handle){

   rewind($handle);
   $title = fgets($handle);
   $title = htmlentities(trim($title)); #ensure text is converted to HTML chars - this data is sent to other functions
   $title = explode( ',' , $title ); # explode at ',' to create array
   return $title;
}

# function to get the student marks from file - which then gets sent to other functinos for validation/grading
function getData($handle){

    rewind($handle); # pointer needs to be at start of file
    fgets($handle); #move past title
    $dataArray = array(); # create data array
    while (!feof($handle)) { #while not at the end of file
        $line = fgets($handle);
        // ensure $line is HTML chars
        $line = htmlentities(trim($line));
        if  (!empty($line))  { # check it is not an empty line
            array_push($dataArray,  $line);
        }
    }
    return $dataArray;
}

/* Function to validate the module code. I'm using substr to break up the module code, then check if it is correct by checking it
against the 'correct' array, or check if it is a digit in the right format */

function validateCourseCode($title){

    $courseCode = strtoupper(trim($title [0])); #strtoupper in case lowercase is used.
    /* i'm using the substr function to break up course code into
    its constituant parts */
    $coursePrefix = substr($courseCode, 0, 2); #courseprefix
    $courseYears = substr($courseCode, 2, 4); #years course is running
    $courseYear1 = substr($courseCode, 2, 2);
    $courseYear2 = substr($courseCode, 4, 2);
    $termCode = substr($courseCode, 6, 2); #code for terms
    // Below are arrays with correct formatting. Used to check  against values obatined from file
    $codes = array('PP', 'P1', 'DT');
    $terms = array('T1', 'T2', 'T3');

    switch (true) {
        case(empty($courseCode)):
        return '<p>Module Code: The module code does not contain any charecters</p>'. PHP_EOL;
        break;

        case(strlen($courseCode) !== 8):
        return '<p>Module Code: The module code does not contain the correct amount of charecters</p>'. PHP_EOL;
        break;
        // check course prefix is in array
        case (!in_array($coursePrefix, $codes)):
        return '<p>Module Code: There is an error in the module code. This is not the correct module prefix</p>'. PHP_EOL;
        break;
        // check the course years are digits,
        case(!ctype_digit($courseYears)) :
        return '<p>Module Code: There is an error in the module code. The middle 4 charecters should designate the academic year the course
        ran </p>'. PHP_EOL;
        break;
        //check that year 2 - year 1 = 1. We need to check they ARE digits before this - otherwise it will throw an error (above)
        case($courseYear2 - $courseYear1 !== 1):
        return '<p>Module Code: There is an error in the module code. The middle 4 charecters should designate the academic year the course
        ran </p>'. PHP_EOL;
        break;
        // check if term code is in array
        case(!in_array($termCode, $terms)):
        return '<p>Module Code: There is an error in the module code. The last two digits should designate the term, ie T1, T2 or T3 </p>'. PHP_EOL;
        break;
        // if no errors return value.
        default: return '<p> Module Code : '. $courseCode . '</p>'. PHP_EOL;
        break;
    }
}


/* Functions to validate programme name and tutor. I'm checking it isn't empty or a digit.
It is easier to check FOR an error, a tutor/programme name will probably not be just letters. ie it will contain space so
looking only for letters will not work */

function validateProgrammeName($title){

    $programmeName = trim($title[1]);  # declare programmeName variable
    if   ((empty($programmeName)) || (ctype_digit($programmeName)))  {
        return '<p> Module Title: The module title is not correct. It does not contain letters </p>'. PHP_EOL;
    }
    else {
        return '<p> Module Title :  ' . trim($programmeName) . '</p>'. PHP_EOL;
    }
}


function validateTutor($title){
    $tutor = trim($title[2]);
    if  ((empty($tutor)) || (ctype_digit($tutor))) {
        return '<p> <p> Tutor : The Tutor\'s name is not correct. It does not contain letters </p>'. PHP_EOL;
    }
    else{
        return '<p> Tutor : ' . trim($tutor) . '</p>'. PHP_EOL;
    }
}


/* Function to validate date. First I check to see if it is 10 chars long and not whitespace.
I have used string replace for punctuation in case of error in date formatting.
Not an issue in current files, but would presumably be
a common error that would break the programme. After exploding at '/' I use the checkdate function to see if date is valid */

function validateDate($title){

    $fullDate = trim($title[3]);
    if((strlen($fullDate) !== 10) || (empty($fullDate)))  {
        return  '<p> Marked date : The date is not formatted correctly. The correct format is DD/MM/YYYY </p>'. PHP_EOL;
    }
    else{
        $punctuationArray = array('.', ',', '\\', ':');
        $fullDate = str_replace($punctuationArray, '/', $fullDate); #replace with '/'
        $dateArray = explode( '/', $fullDate); # Now explode at '/'
        $day = $dateArray[0];
        $month = $dateArray[1];
        $year = $dateArray[2];
        if(checkdate($month, $day, $year) == true){ # in built php function 'checkdate'
            return '<p> Marked date : '. $fullDate . '<p>'. PHP_EOL;
        }
        else{
            return  '<p> Marked date : The date is not formatted correctly. The correct format is DD/MM/YYYY </p>'. PHP_EOL;
        }
    }
}


/* Function to get results from data array. It prevents any data from going to the final marks array if the $line variable
 does not contain a valid student number. If $studentNumber is valid and the
 the mark ($finalData) is in range and a number, it enters the resultsArray */

function getMarks($mainData){

    $resultsArray = array();
    $checkArray = array(',', ' ');
    foreach ($mainData as $key => $value) {
         $line = explode(',', $value);
         $finalData = trim($line[1]);
         $studentNumber = trim($line[0]);
         if ((strlen($studentNumber) == 8) && (ctype_digit($studentNumber))) {
             # check numbers are in acceptable range and ARE digits
              if (($finalData >= 0) && ($finalData <= 100) && (ctype_digit($finalData))){
                    $resultsArray[] = $finalData;
                  }
              }
          }
    return $resultsArray;
}

# Function to get grades. I'm using a for each loop to send data through a switch/case statement.
function getGrades($resultsArray){

    $distinction = 0;
    $merit = 0;
    $pass = 0;
    $fail = 0;
    foreach ($resultsArray as $value){
        switch (true){
            case ($value >=70):
            $distinction ++;
            break;
            case (($value >= 60) && ($value <= 69)):
            $merit ++;
            break;
            case (($value >= 40) && ($value <= 59)):
            $pass ++;
            break;
            case ($value < 40):
            $fail ++;
        }
    }

    return '<p> distinction: ' . $distinction. '</p>'. PHP_EOL .
           '<p> merit: ' .$merit .' </p>'. PHP_EOL.
           '<p> pass: ' . $pass . '</p>'. PHP_EOL.
           '<p> fail: ' . $fail . '</p>'. PHP_EOL;

}

/* Function to print errors and return the reason for the errors to the user. I'm also letting the user know
if there is an error in both mark and student number.  I'm not printing out any of the valid results, only the errors as this
was all that was asked for in the specifications */
function getErrors($mainData){
    $output = '';
    foreach ($mainData as $key => $value) {
        $line = explode(',', $value);
        $marks = trim($line[1]);
        $studentNumber = trim($line[0]);
        switch (true) {
            case (( ($marks < 0) || ($marks > 100) || (!ctype_digit($marks ))) &&
            (((strlen($studentNumber))  !== 8) || (!ctype_digit($studentNumber)))) :
            $output .=  '<p>' . $studentNumber . ' : '. $marks . ' - There is an error in the student number and the mark given </p>'. PHP_EOL;
            break;

            case (empty($studentNumber)):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There is an error in the student number. It has not been assigned </p>'. PHP_EOL;
            break;

            case ((strlen($studentNumber))  !== 8):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There  is an error in the student number. It is not 8 charecters </p>'. PHP_EOL;
            break;

            case ((!ctype_digit($studentNumber))):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There  is an error in the student number. It should just contain numbers </p>'. PHP_EOL;
            break;

            case (empty($marks)):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There is an error in the mark given. A mark has not been assigned </p>'. PHP_EOL;
            break;

            case ((!ctype_digit($marks))):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There is an error in the mark given. It is not a number </p>'. PHP_EOL;
            break;

            case ( ($marks < 0) || ($marks > 100)):
            $output .= '<p>' . $studentNumber . ' : '. $marks . ' - There is an error in the mark given. It is out of range </p>'. PHP_EOL;
            break;
        }
    }
    return $output;
}

/* Function to count errors using for each loop and switch/case to work through all the possible errors in data */
function countErrors($mainData){
    $errors = 0;
    foreach ($mainData as $key => $value) {
        $line = explode(',', $value);
        $marks = trim($line[1]);
        $studentNumber = trim($line[0]);

        switch(true){
            case (!ctype_digit($studentNumber)):
            $errors++;
            break;
            case (strlen($studentNumber) !== 8):
            $errors++;
            break;
            case(!ctype_digit($marks)):
            $errors++;
            break;
            case(($marks < 0) || ($marks > 100)):
            $errors++;
            break;
        }
    }
    return $errors;
}

# Function to count students. I count the array values in $dataArray to get the number
function countStudents($dataArray){
    $students = (count($dataArray));
    return $students;
}

# Seperates the txt files visually. It helped in testing and I feel it makes the presentation clearer.
function seperator(){
    return '<div class = "section-end"> </div>';
}


function mmmr($array, $output = 'mean'){
    #Provides basic statistical functions - default is mean; other $output parammeters are; 'median', 'mode' and 'range'.
	#Ian Hollender 2016 - adapted from the following, as it was an inacurate solution
	#http://phpsnips.com/45/Mean,-Median,-Mode,-Range-Of-An-Array#tab=snippet
	#Good example of PHP overloading variables with different data types - see the Mode code
	if(!is_array($array)){
        echo '<p>Invalid parammeter to mmmr() function: ' . $array . ' is not an array</p>';
		return FALSE; #input parammeter is not an array
    }else{
        switch($output){ #determine staistical output required
            case 'mean': #calculate mean or average
                $count = count($array);
                $sum = array_sum($array);
                $total = $sum / $count;
            break;
            case 'median': #middle value in an ordered list; caters for odd and even lists
                $count = count($array);
				sort($array); #sort the list of numbers
				if ($count % 2 == 0) { #even list of numbers
					$med1 = $array[$count/2];
					$med2 = $array[($count/2)-1];
					$total = ($med1 + $med2)/2;
				}
				else { #odd list of numbers
					$total = $array[($count-1)/2];
				}
            break;
            case 'mode': #most frequent value in a list; N.B. will only find a unique mode or no mode;
                $v = array_count_values($array); #create associate array; keys are numbers in array, values are counts
                arsort($v); #sort the list of numbers in ascending order

				if (count(array_unique($v)) == 1) { #all frequency counts are the same, as array_unique returns array with all duplicates removed!
					return 'No mode';
				}
				$i = 0; #used to keep track of count of associative keys processes
                $modes = '';
				foreach($v as $k => $v){ #determine if a unique most frequent number, or return NULL by only looking at first two keys and frequency numbers in the sorted array
					if ($i == 0) { #first number and frequency in array
						$max1 = $v;	#highest frequency of first number in array
						$modes = $k . ' ';
						$total = $k; #first key is the most frequent number;
					}
					if ($i > 0) { #second number and frequency in array
						$max2 = $v;	#highest frequency of second number in array
						if ($max1 == $max2) { #two or more numbers with same max frequency; return NULL
							$modes = $modes . $k . ' ';
						}
						else {
							break;
						}
					}
					$i++; #next item in $v array to be counted
				}
				$total = $modes;
            break;
            case 'range': #highest value - lowest value
                sort($array); #find the smallest number
                $sml = $array[0];
                rsort($array); #find the largest number
                $lrg = $array[0];
                $total = $lrg - $sml; #calculate the range
            break;
			default :
				echo '<p>Invalid parammeter to mmmr() function: ' . $output . '</p>';
				$total= 0;
				return FALSE;
        }
        return $total;
    }
}


?>
