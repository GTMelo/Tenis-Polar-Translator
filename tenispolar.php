<?php

/*
 * The MIT License
 *
 * Copyright 2015 02364114110.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/** Creates a Tenis Polar object, which serves as a (Very) simplified version 
 * of symmetric-key cryptography, using the Tenis-Polar method (aka Zenit-Polar)
 * 
 * It will take a pair of keys - words with same size and not a single repeating
 * letter (for example, "tenis" and "polar" (aha!). Them it will take a message 
 * (using the 'encrypt' method) and exchange the letters around based on the 
 * relation between the letters of each pair. This is a silly fun way we passed 
 * code messages around in elementary school, and is found in a tween book series
 * written by Pedro Bandeira - The Karas (because poor literacy is Kewl). Not
 * a real-life encryption method, of course. This is just for kicks and practicing
 * PHP, so expect sillyness and unprofessional conduct.
 * 
 * @author GTMelo
 */
class TenisPolar {

    protected $tenis;
    protected $polar;
    protected $status;
    public $validationErrorCodes = array(
        'diffSizes' => 'Keys have different sizes.',
        'notUnique' => 'There are repeating letters somewhere.',
    );

    /** Getter for checking the object's validity. Useful if you are just trying to check if any two words can work.
     * 
     * @return true or an error message.
     */
    public function getStatus() {
        return $this->status;
    }

    /** Generates a new object. Casing is handled by the traduzMensagem method.
     * 
     * @param String $tenis The first key
     * @param String $polar The second key
     */
    function __construct($tenis, $polar) {

        $this->tenis  = strtolower($tenis);
        $this->polar  = strtolower($polar);
        $this->status = $this->isKeyPairValid($this->tenis, $this->polar);
    }

    /** The most important method. Takes a piece of text and encrypts it based on the provided keys on object creation.
     * 
     * @param String $message The text to be encrypted
     * @return String the encrypted text
     * @throws Exception Will fail if there is a problem with the given keys (will tell what).
     */
    public function encrypt($message) {

        $status = $this->getStatus();
        $pieces = str_split($message);

        if ($status == 1) { // 1 instead of true because PHP_hates_you("ECHO_BOOLEAN_SHENANIGANS")
            for ($i = 0; $i < strlen($message); $i++) {
                $pieces[$i] = $this->switchLetter($pieces[$i]);
            }

            $result = implode("", $pieces);

            return $result;
        } else {
            throw new Exception("Error: " . $status);
        }
    }

    /** Executes the actual letter changing, organized by the encrypt method
     * 
     * @param String $target the character provided by encrypt()
     * @return String the character, changed or not depending on case.
     */
    public function switchLetter($target) {

        $tenis   = $this->tenis;
        $polar   = $this->polar;
        $upTenis = mb_strtoupper($tenis);
        $upPolar = mb_strtoupper($polar);

        $array_tenis   = str_split($tenis);
        $array_polar   = str_split($polar);
        $array_upTenis = str_split($upTenis);
        $array_upPolar = str_split($upPolar);

        for ($i = 0; $i < count($array_tenis); $i++) {
            if ($target == $array_tenis[$i]) {
                return $array_polar[$i];
            } else if ($target == $array_polar[$i]) {
                return $array_tenis[$i];
            } else if ($target == $array_upTenis[$i]) {
                return $array_upPolar[$i];
            } else if ($target == $array_upPolar[$i]) {
                return $array_upTenis[$i];
            }
        } return $target; // In case of failing to convert any, return the original character
    }

    /** Validates the given pair of keys.
     * 
     * This function will take the given keys on object creation. 
     * First, it will check for the sizes of the two keys, which have 
     * to be the same. Then, it will check if the two keys have 
     * repeating letters (either on themselves or between each other). 
     * If it passes both tests, returns true. If it fails, it will 
     * dump the appropriate diagnostic.
     * 
     * @param String $tenis The first key
     * @param String $polar The second key
     * @return boolean or one of the possible error messages
     */
    public function isKeyPairValid($tenis, $polar) {

        if ($this->isSameSize($tenis, $polar)) {
            if ($this->isOnlyUniqueLetters($tenis, $polar)) {
                return true;
            } return $this->validationErrorCodes['notUnique'];
        } else {
            return $this->validationErrorCodes['diffSizes'];
        }
    }

    /** Checks if the length of both keys is equal
     * 
     * @param String $tenis
     * @param String $polar
     * @return boolean
     */
    protected function isSameSize($tenis, $polar) {

        if (strlen($tenis) == strlen($polar)) {
            return true;
        } else {
            return false;
        }
    }

    /** Checks if there are no repeating characters
     * 
     * What this will do is take the two given keys, split into arrays and merge
     * them into one. A second variable will take this merged array and remove any
     * duplicates - in this case, that would be duplicate characters. The function
     * will them test the size of the original merged array with the "clean" one.
     * If they are not the same, it means there were duplicates somewhere - either
     * in one of the words (like "roof"), or between the pair (like "house" and 
     * "honed").
     * 
     * @param String $tenis
     * @param String $polar
     * @return boolean
     */
    protected function isOnlyUniqueLetters($tenis, $polar) {

        $tenis                  = str_split($tenis);
        $polar                  = str_split($polar);
        $temp_merge             = array_merge($tenis, $polar);
        $temp_remove_duplicates = array_unique($temp_merge);

        if (count($temp_remove_duplicates) == count($temp_merge)) {
            return true;
        } else {
            return false;
        }
    }

}
