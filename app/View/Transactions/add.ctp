<?php
echo $this->Html->script('transactions/processTransaction');

//format category for input select option category
$catIncome  = $catExpense = array();

foreach ($listCategory as $key => $category) :
    if ($category['Category']['expense_type'] == 'in') :
        $catIncome[$category['Category']['id']] = $category['Category']['name'];
    else :
        $catExpense[$category['Category']['id']] = $category['Category']['name'];
    endif;
endforeach;
?>
<div class="popupForm">
    <?php
    echo $this->Form->create('Transaction', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group',
            ),
        ),
    ));
    ?>
    <div class="form-group error">
        <label for="TransactionCategoryId">Choose category</label>
        <select name="data[Transaction][category_id]" class="form-control" id="TransactionCategoryId">
            <option value="">Choose Category</option>
            <optgroup label="Income">
                <?php
                foreach ($catIncome as $key => $cat) :
                    $isSelected = '';
                    if ($key == $this->request->data['Transaction']['category_id']) :
                        $isSelected = 'selected';
                    endif;
                    echo "<option value='" . $key . "' " . $isSelected . ">" . $cat . "</option>";
                endforeach;
                ?>
            </optgroup>
            <optgroup label="Expense">
                <?php
                foreach ($catExpense as $key => $cat) :
                    $isSelected = '';
                    if ($key == $this->request->data['Transaction']['category_id']) :
                        $isSelected = 'selected';
                    endif;
                    echo "<option value='" . $key . "' " . $isSelected . ">" . $cat . "</option>";
                endforeach;
                ?>
            </optgroup>
        </select>
        <?php if (!empty($validationErrors['category_id'])): ?>
            <div class="error-message">Please select category.</div>
        <?php endif; ?>
    </div>
    <?php
    echo $this->Form->input('amount', array(
        'type'     => 'text',
        'label'    => 'Money',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->input('note', array(
        'type'     => 'textarea',
        'rows'     => 3,
        'style'    => 'resize: vertical;',
        'label'    => 'Note',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->input('create_time', array(
        'type'     => 'text',
        'label'    => 'Time',
        'class'    => 'form-control',
        'default'  => date('Y-m-d', time()),
        'required' => false,
    ));

    echo $this->Form->end(array(
        'label' => 'Create Transaction',
        'div'   => array(
            'class' => 'form-group',
        ),
        'class' => 'btn btn-default',
        'id'    => 'btnTransactionAdd',
    ));
    ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>