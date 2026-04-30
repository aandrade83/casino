

		</div>
    </div>

</div>


<!-- jQuery -->
<script src="../utilities/js/admin/template/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../utilities/js/admin/template/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="../utilities/js/admin/template/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="../utilities/js/admin/template/startmin.js"></script>

<!-- DataTables JavaScript -->
<script src="../utilities/js/admin/template/dataTables/jquery.dataTables.min.js"></script>
<script src="../utilities/js/admin/template/dataTables/dataTables.bootstrap.min.js"></script>

<!-- Page-Level Demo Scripts - Tables - Use for reference -->
<script>
	$(document).ready(function() {
		$('#dataTables-rczun').DataTable({
				responsive: true
		});
	});
</script>

<? if(is_numeric(param("a"))){

	?> <script type="text/javascript">alert("<? echo get_message(param("a")); ?>");</script> <?	
	
} ?>

<script src="../utilities/includes/shadowbox/shadowbox.js"></script>
<script type="text/javascript">Shadowbox.init();</script>

</body>
</html>