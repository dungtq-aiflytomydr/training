<tr>
    <td><img class="img-26px" src="<?php
             if (!empty($category['Category']['icon'])) {
                 echo $category['Category']['icon'];
             } else {
                 echo '/img/building.png';
             }
             ?>"/></td>
    <td><?php echo $category['Category']['name']; ?></td>
    <td><?php
        echo $this->Html->link('Edit', array(
            'controller' => 'categories',
            'action'     => 'edit',
            $category['Category']['id'],
        ));
        ?></td>
    <td><?php
        echo $this->Html->link('Delete', array(
            'controller' => 'categories',
            'action'     => 'delete',
            $category['Category']['id'],
        ));
        ?></td>
</tr>