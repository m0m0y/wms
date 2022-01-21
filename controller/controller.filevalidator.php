<?php 

class FileValidator {

    public static function isUploaded($file, $i = NULL) {
        $case = $_FILES[$file]["error"];
        if(!is_null($i)){ $case = $_FILES[$file]["error"][$i]; }
        switch($case) {
            case UPLOAD_ERR_OK:
                return true;
                break;
            case UPLOAD_ERR_NO_FILE:
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            default:
                return false;
                break;
        }
    }

    public static function allowedSize($file, $size, $i = NULL) {
        
        $case = $_FILES[$file]["size"];
        if(!is_null($i)){ $case = $_FILES[$file]["error"][$i]; }
        return ($case > $size) ? false : true;
    }

    public static function allowedType($file, $extensionArray, $i = NULL) {

        $case = $_FILES[$file]["name"];
        if(!is_null($i)){ $case = $_FILES[$file]["name"][$i]; }
        
        $fileUpload = basename($case);

        $imageFileType = strtolower(pathinfo($fileUpload,PATHINFO_EXTENSION));
        return (in_array($imageFileType,$extensionArray)) ?: false;
    }

    public static function rename($prefix, $file, $i = NULL) {

        $case = $_FILES[$file]["name"];
        if(!is_null($i)){ $case = $_FILES[$file]["name"][$i]; }

        $rename = $prefix.'-'.rand(pow(10, 5-1), pow(10, 5)-1).'-'.str_replace(" ", "-", basename($case));
        $renamed = str_replace(" ", '', $rename);
        return $renamed;
    }

    public static function upload($directory, $file, $fileName, $i = NULL) {

        $case = $_FILES[$file]["tmp_name"];
        if(!is_null($i)){ $case = $_FILES[$file]["tmp_name"][$i]; }

        if (move_uploaded_file($case,$directory.$fileName)) {
            if(Self::allowedType($file, array('jpg', 'jpeg', 'JPG'), $i)) {
                imagejpeg(imagecreatefromjpeg($directory . $fileName), $directory . $fileName, 40);
            }
            return true;
        }
        return false;
    }

    public static function validateFile($file, $size, $extensionArray, $prefix, $directory = '', $upload = false, $i = NULL) {

        if(!Self::isUploaded($file, $i)){
            return "not uploaded";
        }

        if(!Self::allowedSize($file, $size, $i)){
            return "size maximum exceeded";
        }

        if(Self::allowedType($file, $extensionArray, $i)){
            return "forbidden file type :)";
        }



        if(Self::isUploaded($file, $i) && Self::allowedSize($file, $size, $i) && Self::allowedType($file, $extensionArray, $i)) {
            if($upload) {
                $renamed = Self::rename($prefix, $file, $i);
                if (!Self::upload($directory, $file, $renamed, $i)) { return false; }
            }
            return $renamed;
        }
        return false;
    }

}