<?php if ( count($js_vars) > 0 ): ?>
	<script>
		<?php foreach ( $js_vars as $key => $value ): ?>
			<?php
			function is_assoc($array)
			{
				return array_keys($array) !== range(0, count($array) - 1);
			}

			if ( is_array($value) )
			{
				if ( is_assoc($value) )
				{
					echo 'var ', $key, ' = ', json_encode(array_values($value)), ';';

					die('var ' . $key . ' = ' . json_encode(array_values($value)) . ';');
				}
				else
				{
					echo 'var ', $key, ' = ', json_encode($value), ';';
				}
			}
			else
			{
				echo 'var ', $key, ' = ', (is_numeric($value) ? $value : '\'' . $value . '\''), ';';
			}
			?>
		<?php endforeach; ?>
	</script>
<?php endif; ?>