<?php if (!empty($listCategories)): ?>
    <div class="table-responsive cat-in">
        <h3 class="align-center">Income</h3>
        <table class="table table-bordered">
            <thead><tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($listCategories as $category):
                    if ($category['Category']['expense_type'] == 'in'):
                        echo $this->element('categories/item_category', array(
                            'category' => $category,
                        ));
                    endif;
                endforeach;
                ?>
            </tbody>
        </table>
    </div>
    <div class="table-responsive cat-out">
        <h3 class="align-center">Expense</h3>
        <table class="table table-bordered">
            <thead><tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($listCategories as $category):
                    if ($category['Category']['expense_type'] == 'out'):
                        echo $this->element('categories/item_category', array(
                            'category' => $category,
                        ));
                    endif;
                endforeach;
                ?>
            </tbody>
        </table>
    </div>

    <h3 class="align-center">
        <?php
        echo $this->Html->link('Add new category', array(
            'controller' => 'categories',
            'action'     => 'add',
        ));
        ?>
    </h3>
<?php else:
    ?>
    <h3>You have not anything category.</h3>
    <p>
        <?php
        echo $this->Html->link('Add new category', array(
            'controller' => 'categories',
            'action'     => 'add',
        ));
        ?>
    </p>
<?php endif;
