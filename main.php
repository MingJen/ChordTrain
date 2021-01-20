<?php

// songs
$imagine = ['c', 'cmaj7', 'f', 'am', 'dm', 'g', 'e7'];
$somewhere_over_the_rainbow = ['c', 'em', 'f', 'g', 'am'];
$tooManyCooks = ['c', 'g', 'f'];
$iWillFollowYouIntoTheDark = ['f', 'dm', 'bb', 'c', 'a', 'bbm'];
$babyOneMoreTime = ['cm', 'g', 'bb', 'eb', 'fm', 'ab'];
$creep = ['g', 'gsus4', 'b', 'bsus4', 'c', 'cmsus4', 'cm6'];
$army = ['ab', 'ebm7', 'dbadd9', 'fm7', 'bbm', 'abmaj7', 'ebm'];
$paperBag = ['bm7', 'e', 'c', 'g', 'b7', 'f', 'em', 'a', 'cmaj7', 'em7', 'a7', 'f7', 'b'];
$toxic = ['cm', 'eb', 'g', 'cdim', 'eb7', 'd7', 'db7', 'ab', 'gmaj7', 'g7'];
$bulletproof = ['d#m', 'g#', 'b', 'f#', 'g#m', 'c#'];
$song_11 = [];
$songs = [];
$labels = [];
$allChords = [];
$labelCounts = [];
$labelProbabilities = [];
$chordCountsInLabels = [];
$probabilityOfChordsInLabels = [];

function train($chords, $label)
{
    $GLOBALS['songs'][] = [$label, $chords];
    $GLOBALS['label'][] = $label;
    for ($i = 0; $i < count($chords); $i++) {
        if (!in_array($chords[$i], $GLOBALS['allChords'])) {
            $GLOBALS['allChords'][] = $chords[$i];
        }
    }
    if (!!(in_array($label, array_keys($GLOBALS['labelCounts'])))) {
        $GLOBALS['labelCounts'][$label] = $GLOBALS['labelCounts'][$label] + 1;
    } else {
        $GLOBALS['labelCounts'][$label] = 1;
    }
}

function getNumberOfSongs()
{
    return count($GLOBALS['songs']);
}

function setLabelProbabilities()
{
    foreach (array_keys($GLOBALS['labelCounts']) as $label) {
        $numberOfSongs = getNumberOfSongs();
        $GLOBALS['labelProbabilities'][$label] = $GLOBALS['labelCounts'][$label] / $numberOfSongs;
    }
}

function setChordCountsInLabels()
{
    foreach ($GLOBALS['songs'] as $i) {
        if (!isset($GLOBALS['chordCountsInLabels'][$i[0]])) {
            $GLOBALS['chordCountsInLabels'][$i[0]] = [];
        }
        foreach ($i[1] as $j) {
            if ($GLOBALS['chordCountsInLabels'][$i[0]][$j] > 0) {
                $GLOBALS['chordCountsInLabels'][$i[0]][$j] = $GLOBALS['chordCountsInLabels'][$i[0]][$j] + 1;
            } else {
                $GLOBALS['chordCountsInLabels'][$i[0]][$j] = 1;
            }
        }
    }
}

function setProbabilityOfChordsInLabels()
{
    $GLOBALS['probabilityOfChordsInLabels'] = $GLOBALS['chordCountsInLabels'];
    foreach (array_keys($GLOBALS['probabilityOfChordsInLabels']) as $i) {
        foreach (array_keys($GLOBALS['probabilityOfChordsInLabels'][$i]) as $j) {
            $GLOBALS['probabilityOfChordsInLabels'][$i][$j] = $GLOBALS['probabilityOfChordsInLabels'][$i][$j] * 1.0 / count($GLOBALS['songs']);
        }
    }
}

train($imagine, 'easy');
train($somewhere_over_the_rainbow, 'easy');
train($tooManyCooks, 'easy');
train($iWillFollowYouIntoTheDark, 'medium');
train($babyOneMoreTime, 'medium');
train($creep, 'medium');
train($paperBag, 'hard');
train($toxic, 'hard');
train($bulletproof, 'hard');

setLabelProbabilities();
setChordCountsInLabels();
setProbabilityOfChordsInLabels();

function classify($chords){
    $ttal = $GLOBALS['labelProbabilities'];
    print_r($ttal);
    $classified = [];
    foreach (array_keys($ttal) as $obj) {
        $first = $GLOBALS['labelProbabilities'][$obj] + 1.01;
        foreach ($chords as $chord) {
            $probabilityOfChordInLabel = $GLOBALS['probabilityOfChordsInLabels'][$obj][$chord];
            if (!isset($probabilityOfChordInLabel)) {
                $first + 1.01;
            } else {
                $first = $first * ($probabilityOfChordInLabel + 1.01);
            }
            $classified[$obj] = $first;
        }
    }
    print_r($classified);
}

classify(['d', 'g', 'e', 'dm']);
classify(['f#m7', 'a', 'dadd9', 'dmaj7', 'bm', 'bm7', 'd', 'f#m']);