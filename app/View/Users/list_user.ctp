<table>
    <tr>
        <th>Email</th>
        <th>Name</th>
        <th>Role</th>
        <th>Address</th>
    </tr>
    <?php foreach ($listUser as $user): ?>
        <tr>
            <td><?php echo $user['User']['email'];?></td>
            <td><?php echo $this->Html->link($user['User']['name'], array('action' => 'detail/', $user['User']['id']));?></td>
            <td><?php echo $user['User']['role'];?></td>
            <td><?php echo $user['User']['address'];?></td>
        </tr>
    <?php endforeach;?>
</table>