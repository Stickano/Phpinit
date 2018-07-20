<?php

class StringHandler {

    /**
     * Searches through a string for certain keywords
     * @param  string $content     The string to search through
     * @param  string $search      What to search for
     * @param  array  $replaceWith What to replace with (passed on to replace())
     * @return string|array        If $replaceWith is not provided, an array with the places is returned
     *                             Else the formatted text, from replace(), is returned
     */
    public function multiSearch(string $content, string $search, array $replaceWith=null){
        $br         = 0;
        $arrayCount = 0;
        $lastPos    = -1;
        $positions  = array();
        $dataPoint  = array();

        while(($pos = strpos($content,$search,$br)) !== false) {
            if($arrayCount == 2){
                $arrayCount = 0;
                $positions[] = $dataPoint;
                $dataPoint=array();
            }
            if($pos != $lastPos){
                $dataPoint[] = $pos;
                $arrayCount++;
            }
            $lastPos = $pos;
            $br++;
        }

        if(is_array($replaceWith) && count($replaceWith) == 2)
            return self::multiReplace($content, $positions, $replaceWith);
        else
            return $positions;
    }

    /**
     * Replaces part of a string with something else
     * @param  string $content     The text to replace content in
     * @param  array  $positions   Which positions to replace (start => stop)
     * @param  array  $replaceWith Replace with this content (startContent => stopContent)
     * @return string              The formatted text
     */
    private function multiReplace(string $content, array $positions, array $replaceWith){
        $txt = $content;
        $result = array_reverse($positions, true);
        foreach ($result as $key) {
            $br = 1;
            $key = array_reverse($key, true);
            foreach ($key as $sub => $value) {
                if($br == 2)
                    $br = 0;

                if($br==0)
                    $replace = $replaceWith[0];
                else
                    $replace = $replaceWith[1];

                $txt = substr_replace($txt, $replace, $key[$br], 5);
                $br++;
            }
        }

        return $txt;
    }

    /**
     * Search a string for certain words -
     * Allows you to replace the string with something else
     * @param  string      $content     The string to search through
     * @param  string      $search      What to search for
     * @param  string|null $replaceWith What to replace with
     * @return string|array             The formatted string (replace), or an array of positions
     */
    public function search(string $content, string $search, string $replaceWith=null){
        $br         = 0;
        $positions  = array();
        $strLen = strlen(htmlspecialchars($search));
        while(($pos = strpos($content,"\r\n",$br)) !== false) {
            $positions[] = $pos;
            $br++;
        }

        if($replaceWith != null)
            return self::replace($content, $positions, $replaceWith, $strLen);
        else
            return $positions;
    }

    /**
     * Replace part of a string with somethig else
     * @param  string $content     The string to replace in
     * @param  array  $positions   The positions to replace something
     * @param  string $replaceWith What to replace with
     * @param  int    $strLen      The length of the string that's about to be replaced
     * @return string              The formatted string
     */
    private function replace(string $content, array $positions, string $replaceWith, int $strLen){
        $txt = $content;
        $result = array_reverse($positions, true);
        foreach ($result as $key) {
            $txt = substr_replace($txt, $replaceWith, $key, 1);
        }
        return $txt;
    }
}

?>