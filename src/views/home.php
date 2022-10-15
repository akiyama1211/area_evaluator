
<h3>住所入力欄</h3>
<?php if (count($errors)): ?>
    <ul>
        <?php foreach ($errors as $error):?>
            <li><?php echo $error?></li>
        <?php endforeach?>
    </ul>
<?php endif?>

<script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
<form action="search.php" method="POST" class="h-adr">
    <span class="p-country-name" style="display:none;">Japan</span>
    郵便番号：<input type="text" class="p-postal-code" size="8" maxlength="8"><br>
    <p>郵便番号を入力すると町名まで自動的に入力されます。</p>
    <p>郵便番号がわからない方は<a href="http://www.post.japanpost.jp/zipcode/" target="_blank" rel="noopener noreferrer">こちら</a></p>
    都道府県[必須項目]：<input type="text" class="p-region" name="prefectures" value="<?php echo $prefectures?>" /><br>
    市区町村[必須項目]：<input type="text" class="p-locality" name="municipalities" value="<?php echo $municipalities?>" /><br>
    町名：<input type="text"; class="p-street-address" name="street" value="<?php echo $street?>" /><br>
    番地：<input type="text"; class="p-extended-address" name="address" value="<?php echo $extendAddress?>" />
    <div>
        <button type="submit">解析開始</button>
    </div>
</form>

<a href="explain.php">使い方</a>
