<?php if ( count($js_vars) > 0 ): ?>
	<script>
		<?php foreach ( $js_vars as $key => $value ): ?>
			<?php
			if ( is_array($value) )
			{
				echo 'var ', $key, ' = ', json_encode($value), ';';
			}
			else
			{
				echo 'var ', $key, ' = ', (is_numeric($value) ? $value : '\'' . $value . '\''), ';';
			}
			?>
		<?php endforeach; ?>
	</script>
<?php endif; ?>