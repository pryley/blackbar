<?php defined( 'WPINC' ) || die; ?>

<pre><code class="php">$_GET = <?= esc_html( var_export( $_GET, true )); ?>;</code></pre><br/>
<pre><code class="php">$_POST = <?= esc_html( var_export( $_POST, true )); ?>;</code></pre><br/>
<pre><code class="php">$_COOKIE = <?= esc_html( var_export( $_COOKIE, true )); ?>;</code></pre><br/>
<pre><code class="php">$_SESSION = <?= esc_html( var_export( isset( $_SESSION ) ? $_SESSION : [], true )); ?>;</code></pre><br/>
<pre><code class="php">$_SERVER = <?= esc_html( var_export( $_SERVER, true )); ?>;</code></pre><br/>
