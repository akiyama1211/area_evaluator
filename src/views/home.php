
<h3>住所入力欄</h3>
<?php if (isset($_GET['error'])):?>
    <?php $errors = $_GET['error']?>
    <?php if (in_array('noPrefectures', $errors)):?>
        <p>都道府県を入力してください。</p>
    <?php endif?>
    <?php if (in_array('noMunicipalities', $errors)):?>
        <p>市区町村を入力してください。</p>
    <?php endif?>
    <?php if (in_array('noStreet', $errors)):?>
        <p>町名を入力してください。</p>
    <?php endif?>
    <?php if (in_array('noResult', $errors)):?>
        <p>該当の住所が存在しないため、解析に失敗しました。</p>
    <?php endif?>
<?php endif?>
<script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
<form action="search.php" method="POST" class="h-adr">
    <span class="p-country-name" style="display:none;">Japan</span>
    郵便番号：<input type="text" class="p-postal-code" size="8" maxlength="8"><br>
    <p>郵便番号を入力すると町名まで入力されます。</p>
    <p><a href="http://www.post.japanpost.jp/zipcode/" target="_blank" rel="noopener noreferrer">郵便番号がわからない方はこちら</a></p>
    都道府県：<input type="text" class="p-region" name="prefectures"/><br>
    市区町村：<input type="text" class="p-locality" name="municipalities" /><br>
    町名：<input type="text"; class="p-street-address" name="street"/><br>
    番地：<input type="text"; class="p-extended-address" name="address" />
    <div>
        <button type="submit">解析開始</button>
    </div>
</form>

<a href="explain.php">使い方</a>

<!-- <?php if (count($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $error):?>
            <li>
                <?php echo $error?>
            </li>
            <?php endforeach?>
        </ul>
        <?php endif?> -->
