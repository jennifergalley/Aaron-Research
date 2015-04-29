<?php
    require_once ("../config/global.php");
    require_once ($header);
    
    require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
    use google\appengine\api\cloud_storage\CloudStorageTools;
    
    $options = array('gs_bucket_name' => 'aarons-tests/images', 'max_bytes_per_blob' => 1048576);
    $upload_url = CloudStorageTools::createUploadUrl('/admin/uploadHandler.php', $options);
?>
<a href="admin.php" target="_self" class="back">Admin &lt;&lt;</a>

    <h1>Upload Images</h1>
    <h3>Please note that the maximum image size is 1MB. Duplicate images will not be stored, so be sure your image names are unique. You may upload multiple image files at once.</h3>
    <form method="post" action="<?php echo $upload_url; ?>" enctype="multipart/form-data">
        <table class='form'>
            <tr>
                <td><label for="file">Images:</label></td>
                <td><input required type="file" name="file[]" multiple></td>
            </tr>
        </table>
        <br>
        <input type="submit" name="submit" value="Submit">
    </form>
    
<?php
    require_once ($footer);
?>