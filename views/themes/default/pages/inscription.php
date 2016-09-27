<div id="global">
<div id="inscription">
  <h2>Inscription :</h2>
  <div id="erreur">
  <?php
  global $erreur;
    if (isset($erreur)){
      echo '<h4>Erreur : '. $erreur . '</h4>';
    } ?>
  </div>
  <form action="" method="post">
  <table>
    <tr>
      <td>Votre pseudo : </td><td><input type="pseudo" name="pseudo_inscription" placeholder="Votre pseudo in-game exact" size="70%"></td>
    </tr>
    <tr>
      <td>Votre email : </td><td><input type="email" name="email_inscription" placeholder="Merci d'entrer un email valide" size="70%"></td>
    </tr>
    <tr>
      <td>Votre mot de passe : </td><td><input type="password" name="mp_inscription" placeholder="Merci d'entrer un mot de passe de plus de 6 caractères" size="70%"></td>
    </tr>
    <tr>
      <td>Répétez le mot de passe : </td><td><input type="password" name="mp2_inscription" placeholder="Répétez-le" size="70%"></td>
    </tr>
  </table>
    <p class="news"><input type="checkbox" name="news" checked>S'abonner à la news-letter</p></td>
    <p class="sub_inscription"><input type="submit" value="Valider" class="sub_inscription"></p>
  </form>
</div>
<div id="connexion">
  <h2>Connexion :</h2>
  <form action="" method="post">
    <table>
      <tr>
        <td>Votre pseudo : </td><td><input type="pseudo" name="pseudo_connexion" size="70%"></td>
      </tr>
      <tr>
        <td>Votre mot de passe : </td><td><input type="password" name="mp_connexion" size="70%"></td>
      </tr>
    </table>
    <p class="souvenir"><input type="checkbox" name="souvenir" checked>Se souvenir de moi</p></td>
    <p class="sub_connexion"><input type="submit" value="Valider" class="sub_connexion"></p>
  </form>
</div>
</div>
<!--<div id="connexion">
  <form action="" method="post">
    <p>Votre pseudo (inGame pour la boutique) : <input type="pseudo" name="pseudo_inscription"></p>
    <p>Votre email : <input type="email" name="email_inscription"></p>
    <p>Votre mot de passe (plus de 6 caractères): <input type="password" name="mp_inscription"></p>
    <p>Répétez le mot de passe : <input type="password" name="mp2_inscription"></p>
    <input type="submit" value="Valider">
  </form>
</div>-->
