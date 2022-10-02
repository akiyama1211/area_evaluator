<h3>住所入力欄</h3>

<script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
<form action="search.php" method="POST" class="h-adr">
    <span class="p-country-name" style="display:none;">Japan</span>
    郵便番号：<input type="text" class="p-postal-code" size="8" maxlength="8"><br>
    都道府県：<input type="text" class="p-region" name="prefectures"/><br>
    市区町村：<input type="text" class="p-locality" name="municipalities" /><br>
    町名：<input type="text"; class="p-street-address" name="street"/><br>
    番地：<input type="text"; class="p-extended-address" name="address" />
    <div>
        <button type="submit">送信する</button>
    </div>
</form>


    <!-- <?php if (count($errors) > 0): ?>
        <ul>
            <?php foreach ($errors as $error):?>
                <li>
                    <?php echo $error?>
                </li>
                <?php endforeach?>
            </ul>
            <?php endif?> -->
