<h4><?= $userName ?></h4>
<p>平均 <?php echo sprintf('%.1f', $avgRating); ?></p>
<table>
    <thead>
        <tr>
            <th>コメント</th>
        </tr>
    </thead>
    <?php foreach ($ratingComments->toArray() as $com) : ?>
        <tr>
            <td><?= $com['rating_comment'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
