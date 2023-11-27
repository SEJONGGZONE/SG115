<template>
  <section
    :class="`extra__info transition-3 ${showSidebar ? 'info-opened' : ''}`"
  >
    <div class="extra__info-inner">
      <div class="extra__info-close text-end" @click="showSidebar = false">
        <a @click.prevent="showSidebar = false" href="#" class="extra__info-close-btn">
          <i class="fal fa-times"></i>
        </a>
      </div>

      <!-- side-mobile-menu start -->
      <nav class="side-mobile-menu d-block mm-menu">
        <ul>
          <template v-for="(menu, i) in mobile_menus" :key="i">
            <li v-if="menu.dropdownItems" :class="`menu-item-has-children has-droupdown ${activeMenu === menu.title ? 'active' : ''}`">
              <a @click.prevent="handleOpenMenu(menu.title)">{{menu.title}}</a>
              <ul @click.prevent="showSidebar = false" :class="`sub-menu ${activeMenu === menu.title ? 'active' : ''}`">
                <li v-for="(sub_m, index) in menu.dropdownItems" :key="index" >
                  <!--<nuxt-link :href="`${sub_m.link}`">{{sub_m.title}}</nuxt-link>-->
                  <a @click.prevent="litleMenu(sub_m.link)">{{ sub_m.title }}</a>
                </li>
              </ul>
            </li>

            <li v-if="!menu.dropdownItems">
              <!--<nuxt-link :href="`${menu.link}`">{{ menu.title }}</nuxt-link>-->
              <a @click.prevent="litleMenu(menu.link)" href="">{{ menu.title }}</a>
            </li>
          </template>
        </ul>
      </nav>
      <!-- side-mobile-menu end -->
    </div>
  </section>

  <!--  body overlay  -->
  <div
    @click="showSidebar = false"
    :class="`body-overlay transition-3 ${showSidebar ? 'opened' : ''}`"
  ></div>
</template>

<script >
import { defineComponent, ref } from "vue";
import menuData from "@/mixins/menuData";

// menu type 

export default defineComponent({
  mixins: [menuData],
  data() {
    return {
      activeMenu: "",
      showSidebar: false,
      mobile_menus: '',
    };
  },
  methods: {
    OpenOffcanvas(mainList) {
      this.showSidebar = true;
      this.mobile_menus = mainList;
      console.log("mainList:::",mainList);
    },
    handleOpenMenu(navTitle) {
      if (navTitle === this.activeMenu) {
        this.activeMenu = "";
      } else {
        this.activeMenu = navTitle;
      }
    },
    litleMenu(link){
      location.href = link
    }
  }
});
</script>
