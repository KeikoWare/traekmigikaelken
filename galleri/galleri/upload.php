<h1>Upload jpg billeder</h1>
Filstørrelse max 1 mb, og <b>kun</b> jpg filer.
<form enctype="multipart/form-data" accept="image/jpeg" action="galleri/done.php" method="post" >
<input type="hidden" name="max_file_size" value="1000000">
<input type="hidden" name="User" value="<?=$navn?>">
Billede: <input class="txt" name="userfile" type="file" ><br><br>
Tekst til : <textarea name="Tekst" rows=4 cols=25></textarea><br><br>
<input class="but" type="submit" value="Upload billede" ><br>
</form>
