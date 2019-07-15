<style>
code{
  background-color:#eeeeee;
  white-space:pre;
}
</style>
hello<br>
<table>
<thead>
<tr>
  <th>Name</th>
  <th>Value</th>
</tr>
</thead>
<tbody>
<?php foreach ($_GET as $k => $v) { ?>
<tr>
  <td><code><?=htmlspecialchars($k)?></code></td>
  <td><code><?=$v ? htmlspecialchars($v) : "<span style='color:red'>none</span>"?></code></td>
</tr>
<?php } ?>
</tbody>
</table>