<section class="awvideobanner">
  {if $awvideobanner_muted}
    <video autoplay loop playsinline muted>
      <source src="{$awvideobanner_video_url|escape:'html':'UTF-8'}" type="{$awvideobanner_video_type|escape:'html':'UTF-8'}">
    </video>
  {else}
    <video loop playsinline controls>
      <source src="{$awvideobanner_video_url|escape:'html':'UTF-8'}" type="{$awvideobanner_video_type|escape:'html':'UTF-8'}">
    </video>
  {/if}
</section>
