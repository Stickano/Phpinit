<?php

class Dir {

	/**
	 * List Files & Folders in a given Directory
	 * @param  string $dir Hold the path to the directory
	 * @return array      Folders first, then files.
	 */
	public function ls($dir) {
		if(!is_dir($dir))
			return "Directory not found";

        $content = array_diff(scandir($dir), array('.', '..'));
        $folders = array();
        $files   = array();
        $result  = array();

		# Sort folder and files
		foreach ($content as $item) {
			if(is_dir($dir.'/'.$item))
				$folders[] = $item."/";
			else
				$files[] = $item;
		}

		# Loop through the content, folder first
		sort($folders);
		if(!empty($folders)){
			foreach ($folders as $folder) {
				$result[] = $folder;
			}
		}

		sort($files);
		if(!empty($files)){
			foreach ($files as $file) {
				$result[] = $file;
			}
		}

        return $result;
	}


	/**
     * Removes a folder and its content
     * @param  string $dir The directory to remove
     * @return             No return value
     */
	public function rrmdir($dir) {

        if (!is_dir($dir))
            throw new Exception("Directory not found");

        $objects = scandir($dir);
        foreach ($objects as $object) {

            if ($object == "." && $object == "..")
                continue;

            if (filetype($dir."/".$object) == "dir")
                rrmdir($dir."/".$object);
            else
            	unlink($dir."/".$object);
        }

        reset($objects);
        rmdir($dir);
    }
}

?>