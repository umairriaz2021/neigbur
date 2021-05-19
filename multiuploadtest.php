

<!DOCTYPE html>
<html>
   <head>
      <title>Upload Multiple Images Using jquery and PHP</title>
      <!-------Including jQuery from Google ------>
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script>
         var abc = 0;      // Declaring and defining global increment variable.
         $(document).ready(function() {
         //  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
         $('#add_more').click(function() {
         $(this).before($("<div/>", {
         id: 'filediv'
         }).fadeIn('slow').append($("<input/>", {
         name: 'file[]',
         type: 'file',
         id: 'file'
         }), $("<br/><br/>")));
         });
         // Following function will executes on change event of file input to select different file.
         $('body').on('change', '#file', function() {
         if (this.files && this.files[0]) {
         abc += 1; // Incrementing global variable by 1.
         var z = abc - 1;
         var x = $(this).parent().find('#previewimg' + z).remove();
         $(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src=''/></div>");
         var reader = new FileReader();
         reader.onload = imageIsLoaded;
         reader.readAsDataURL(this.files[0]);
         $(this).hide();
         $("#abcd" + abc).append($("<img/>", {
         id: 'img',
         src: 'x.png',
         alt: 'delete'
         }).click(function() {
         $(this).parent().parent().remove();
         }));
         }
         });
         // To Preview Image
         function imageIsLoaded(e) {
         $('#previewimg' + abc).attr('src', e.target.result);
         };
         $('#upload').click(function(e) {
         var name = $(":file").val();
         if (!name) {
         alert("First Image Must Be Selected");
         e.preventDefault();
         }
         });
         });
      </script>
      <!------- Including CSS File ------>
      <style>
         @import "http://fonts.googleapis.com/css?family=Droid+Sans";
         form{
         background-color:#fff
         }
         #maindiv{
         width:960px;
         margin:10px auto;
         padding:10px;
         font-family:'Droid Sans',sans-serif
         }
         #formdiv{
         width:500px;
         float:left;
         text-align:center
         }
         form{
         padding:40px 20px;
         box-shadow:0 0 10px;
         border-radius:2px
         }
         h2{
         margin-left:30px
         }
         .upload{
         background-color:red;
         border:1px solid red;
         color:#fff;
         border-radius:5px;
         padding:10px;
         text-shadow:1px 1px 0 green;
         box-shadow:2px 2px 15px rgba(0,0,0,.75)
         }
         .upload:hover{
         cursor:pointer;
         background:#c20b0b;
         border:1px solid #c20b0b;
         box-shadow:0 0 5px rgba(0,0,0,.75)
         }
         #file{
         color:green;
         padding:5px;
         border:1px dashed #123456;
         background-color:#f9ffe5
         }
         #upload{
         margin-left:45px
         }
         #noerror{
         color:green;
         text-align:left
         }
         #error{
         color:red;
         text-align:left
         }
         #img{
         width:17px;
         border:none;
         height:17px;
         margin-left:-20px;
         margin-bottom:91px
         }
         .abcd{
         text-align:center
         }
         .abcd img{
         height:100px;
         width:100px;
         padding:5px;
         border:1px solid #e8debd
         }
         b{
         color:red
         }
      </style>
   <body>
      <div id="maindiv">
         <div id="formdiv">
            <h2>Multiple Image Upload Form</h2>
            <form enctype="multipart/form-data" action="" method="post">
               First Field is Compulsory. Only JPEG,PNG,JPG Type Image Uploaded. Image Size Should Be Less Than 100KB.
               <div id="filediv"><input name="file[]" type="file" id="file"/></div>
               <input type="button" id="add_more" class="upload" value="Add More Files"/>
               <input type="submit" value="Upload File" name="submit" id="upload" class="upload"/>
            </form>
            <!------- Including PHP Script here ------>
            <?php
               if (isset($_POST['submit'])) {
               $j = 0;     // Variable for indexing uploaded image.
               $target_path = "uploads/";     // Declaring Path for uploaded images.
               for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
               // Loop to get individual element from the array
               $validextensions = array("jpeg", "jpg", "png");      // Extensions which are allowed.
               $ext = explode('.', basename($_FILES['file']['name'][$i]));   // Explode file name from dot(.)
               $file_extension = end($ext); // Store extensions in the variable.
               $target_path = $target_path . md5(uniqid()) . "." . $ext[count($ext) - 1];     // Set the target path with a new name of image.
               $j = $j + 1;      // Increment the number of uploaded images according to the files in array.
               if (($_FILES["file"]["size"][$i] < 100000)     // Approx. 100kb files can be uploaded.
               && in_array($file_extension, $validextensions)) {
               if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path)) {
               // If file moved to uploads folder.
               echo $j. ').<span id="noerror">Image uploaded successfully!.</span><br/><br/>';
               } else {     //  If File Was Not Moved.
               echo $j. ').<span id="error">please try again!.</span><br/><br/>';
               }
               } else {     //   If File Size And File Type Was Incorrect.
               echo $j. ').<span id="error">***Invalid file Size or Type***</span><br/><br/>';
               }
               }
               }
               ?>
         </div>
      </div>
   </body>
</html>

