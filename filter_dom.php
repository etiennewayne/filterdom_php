    /* ============================== 
        this method return the content
        change the <img src=(base64) /> to <img src="/storage_path/your_dir" />
    */
    private function filterDOM($content){
        $modifiedHtml = '';

        $doc = new \DOMDocument('1.0', 'UTF-8'); //solution add bacbkward slash
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $doc->encoding = 'UTF-8';
        $htmlContent = $content;
        $doc->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
        //$doc->loadHTML($htmlContent);

        $images = $doc->getElementsByTagName('img');
        // Find all img tags
        $counter = 0;

        foreach ($images as $image) {

            $src = $image->getAttribute('src');
            $currentTimestamp = time(); // Get the current Unix timestamp
            $md5Hash = md5($currentTimestamp . $counter); // Create an MD5 hash of the timestamp

            // Check if the src is a data URL (Base64)
            if (strpos($src, 'data:image/') === 0) {
                // Extract image format (e.g., png, jpeg) detect fileFormat
                $imageFormat = explode(';', explode('/', $src)[1])[0];

                // Modify the src to point to the directory where the image is stored
                //$imageName = $imgPath . $md5Hash . '.' . $imageFormat; // Replace with your logic for generating unique filenames
                $imageName = $md5Hash . '.' . $imageFormat; // Replace with your logic for generating unique filenames
                
                //file_put_contents($this->uploadPath, base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $src))); // Save the image
                file_put_contents($this->uploadPath . '/' . $imageName, base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $src))); // Save the image

                // Set the new src attribute
                //concat '/' for directory
                $image->setAttribute('src', '/'.$this->uploadPath.'/' . $imageName);
            }
            //to make image name unique add counter in time and hash the time together with the counter
            $counter++;
        }
        //save all changes
        $modifiedReviseImg = $doc->saveHTML();

        //removing all html,header //only tag inside the body will be saved
        $newDocImg =  new \DOMDocument('1.0', 'UTF-8'); //solution add bacbkward slash
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $newDocImg->encoding = 'UTF-8';
        //$newDocImg->loadHTML($modifiedReviseImg);
        $newDocImg->loadHTML(mb_convert_encoding($modifiedReviseImg, 'HTML-ENTITIES', 'UTF-8'));

        // Find the <body> tag
        $bodyNode = $newDocImg->getElementsByTagName('body')->item(0);

        if ($bodyNode !== null) {
            // Create a new document for the content inside <body>
            $newDoc = new \DOMDocument();
            foreach ($bodyNode->childNodes as $node) {
                $newNode = $newDoc->importNode($node, true);
                $newDoc->appendChild($newNode);
            }

            // Output the content inside <body>
            $modifiedHtml = $newDoc->saveHTML();
        }

        return $modifiedHtml;
    }
