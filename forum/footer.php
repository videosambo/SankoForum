			</div> <!-- End of the container--> 
		</div> <!-- End of the wrapper-->
		<div id="footer">Kaikki oikeudet pidetään</div>
		<?php displayNotifications(); ?>
	</body>
	<script src="ckeditor5/ckeditor.js"></script>
	<script>
		//Teksti editori
		ClassicEditor.create(document.getElementById('text_editor')).catch( error => {
            console.error(error);
        });
	</script>
</html>