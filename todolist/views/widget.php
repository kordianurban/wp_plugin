<?php
/**
 * ToDo List Widget
 * Displays ToDo List
 */

$list = get_query_var( 'list' ); ?>

<form id="todolist" action="" method="post">
    <ul>
        <li data-id="0">
            <span></span>
            <input name="task-status" type="checkbox" value="complete" />
            <input name="task-name" type="text" value="" placeholder="Enter new task here..." />
            <a href="#delete">&times;</a>
        </li>

        <?php if ( $list->have_posts() ):
            while ( $list->have_posts() ): $list->the_post();
                $status = get_post_meta( $post->ID, ToDoList::META_STATUS, true ); ?>

                <li data-id="<?php echo $post->ID; ?>"<?php echo $status == 'complete' ? ' class="checked"' : ''; ?>>
                    <span></span>
                    <input name="task-status" type="checkbox" value="complete"<?php echo $status == 'complete' ? ' checked' : ''; ?> />
                    <input name="task-name" type="text" placeholder="Enter new task here..." value="<?php the_title(); ?>" />
                    <a href="#delete">&times;</a>
                </li>

            <?php endwhile;
        endif; ?>
    </ul>
</form>
