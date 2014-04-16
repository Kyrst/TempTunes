<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Username</th>
			<th>Name</th>
			<th>Songs</th>
			<th>Friends</th>
			<th>Plan</th>
			<th>Num Logins</th>
			<th>Last Login</th>
			<th>Registered</th>
			<th>Manage</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $users as $_user ): ?>
			<tr>
				<td><?= $_user->id ?></td>
				<td><?= $_user->username ?></td>
				<td><?= $_user->get_name() ?></td>
				<td><a href="<?= $_user->get_link(User::URL_SONGS) ?>"><?= $_user->songs->count() ?></a></td>
				<td><a href="<?= $_user->get_link(User::URL_FRIENDS) ?>"><?= $_user->friends->count() ?></a></td>
				<td><?= $_user->get_plan_str() ?></td>
				<td><?= $_user->num_logins ?></td>
				<td><?= $_user->last_login !== NULL ? $_user->last_login : '/' ?></td>
				<td><?= $_user->created_at ?></td>
				<td>
					<div class="btn-group">
						<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
							Action <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:" class="btn-xs">Edit</a></li>
							<?php if ( $_user->id !== $user->id ): ?><li><a href="javascript:" class="btn-xs">Delete</a></li><?php endif ?>
						</ul>
					</div>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>