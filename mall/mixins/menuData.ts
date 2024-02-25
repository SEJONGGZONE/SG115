import menuType from "~/types/menuType";

export default {
  data() {
    return {
      menuData: [
        {
          link: '/categoryList?code=01&title=냉동식품',
          title: '카테고리',
          hasDropdown: true,
          megamenu: false,
          dropdownItems: [
            { link: '/categoryList?code=01&title=냉동식품', title: '냉동식품', code : '01' },
            { link: '/categoryList?code=02&title=냉장식품', title: '냉장식품', code : '02' },
            { link: '/categoryList?code=03&title=쌀/단무지/면/김치', title: '쌀/단무지/면/김치', code : '03' },
            { link: '/categoryList?code=04&title=장류/식용유/가루', title: '장류/식용유/가루', code : '04' },
            { link: '/categoryList?code=05&title=캔/통조림/반찬', title: '캔/통조림/반찬', code : '05' },
            { link: '/categoryList?code=06&title=소스/드레싱/향신료', title: '소스/드레싱/향신료', code : '06' },
            { link: '/categoryList?code=07&title=농산물/수산물', title: '농산물/수산물', code : '07' },
            { link: '/categoryList?code=08&title=커피/차/음료', title: '커피/차/음료', code : '08' },
            { link: '/categoryList?code=09&title=치즈/버터/유제품', title: '치즈/버터/유제품', code : '09' },
            { link: '/categoryList?code=10&title=용기/세제/잡화', title: '용기/세제/잡화', code : '10' },
          ]
        },
        {
          link: '/eventList?code=10&title=임박 특가',  
          code : '10' ,
          title: '임박 특가',
        },
        {
          link: '/eventList?code=20&title=추천 상품',  
          code : '20' ,
          title: '추천 상품',
        },
        {
          link: '/eventList?code=30&title=업소 대용량',  
          code : '30' ,
          title: '업소 대용량',
        },
        {
          link: '/eventList?code=40&title=쿡짱 전용',  
          code : '40' ,
          title: '쿡짱 전용',
        },
        {
          link: '/companyInfo',
          title: '회사소개',
        },
        {
          link: '/consult',
          hasDropdown: false,
          title: '입점/제휴문의',
        },
      ] as menuType[]
    }
  }
}