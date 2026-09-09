/**
 * مجموعة إياس الإعلامية - بيانات الهيكل الوظيفي
 */

window.EMS_DATA = {
  organization: {
    name: "مجموعة إياس الإعلامية",
    name_en: "EYAS Media Group",
    system: "نظام إدارة مجموعة إياس (EMS)",
    // Version: 1.0
    super_admin: "صالح رزق محمد حسنين (رئيس مجلس الإدارة)"
  },
  
  // القيادة العليا
  leadership: [
    {
      id: "EMP-00",
      emp_code: "EMP-00",
      name: "صالح رزق محمد حسنين",
      role: "رئيس مجلس الإدارة (Super Admin)",
      dept: "مجلس الإدارة العليا",
      scope: "Full Scope",
      email: "saleh@eyasmedia.com",
      phone: "+970 59 900 0001",
      status: "نشط",
      avatar: "ص"
    },
    {
      id: 1,
      emp_code: "1",
      name: "محمد ابو شحمه",
      role: "المدير التنفيذي (CEO)",
      dept: "الإدارة العليا والتنفيذية",
      scope: "Executive Scope",
      email: "m.abushahma@eyasmedia.com",
      phone: "+970 59 900 0002",
      status: "نشط",
      avatar: "م"
    },
    {
      id: 2,
      emp_code: "2",
      name: "براء عمر",
      role: "المستشار القانوني ونائب المدير التنفيذي",
      dept: "الإدارة العليا والتنفيذية",
      scope: "Executive Scope",
      email: "b.omar@eyasmedia.com",
      phone: "+970 59 900 0003",
      status: "نشط",
      avatar: "ب"
    },
    {
      id: 3,
      emp_code: "3",
      name: "رواء صلاح",
      role: "السكرتارية التنفيذية ومتابعة المكاتب",
      dept: "الإدارة العليا والتنفيذية",
      scope: "Executive Scope",
      email: "r.salah@eyasmedia.com",
      phone: "+970 59 900 0004",
      status: "نشط",
      avatar: "ر"
    }
  ],

  // دائرة الإعلام
  media_dept: [
    {
      id: 4,
      emp_code: "4",
      name: "البراء ابو عامر",
      role: "مسؤول قسم المرئي والإنتاج التلفزيوني",
      dept: "دائرة الإعلام",
      section: "قسم المرئي",
      scope: "Department Scope",
      email: "b.abuamer@eyasmedia.com",
      phone: "+970 59 900 0005",
      status: "نشط",
      avatar: "ب"
    },
    {
      id: 5,
      emp_code: "5",
      name: "لؤي بركة",
      role: "منسق الإعلام الرقمي والمنصات",
      dept: "دائرة الإعلام",
      section: "منسق الرقمي",
      scope: "Department Scope",
      email: "l.baraka@eyasmedia.com",
      phone: "+970 59 900 0006",
      status: "نشط",
      avatar: "ل"
    }
  ],

  // دائرة البرمجة والتقنية (15 موظفاً: من 6 إلى 20)
  tech_dept: [
    { id: 6, emp_code: "6", name: "أحمد حبيب", role: "رئيس قسم الويب ومطور Full Stack", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "a.habib@eyasmedia.com", status: "نشط", avatar: "أ" },
    { id: 7, emp_code: "7", name: "محمود المصري", role: "مطور أنظمة خلفية (Senior Backend Laravel)", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "m.masri@eyasmedia.com", status: "نشط", avatar: "م" },
    { id: 8, emp_code: "8", name: "إبراهيم الصالحي", role: "مطور واجهات أمامية تفاعلية (Frontend)", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "i.salhi@eyasmedia.com", status: "نشط", avatar: "إ" },
    { id: 9, emp_code: "9", name: "سارة الخزندار", role: "مهندسة تصميم واجهات وتجربة مستخدم UI/UX", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "s.khazendar@eyasmedia.com", status: "نشط", avatar: "س" },
    { id: 10, emp_code: "10", name: "أنس الدلو", role: "مطور واجهات ومكونات الويب", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "a.dalou@eyasmedia.com", status: "نشط", avatar: "أ" },
    { id: 11, emp_code: "11", name: "بلال قاسم", role: "مطور حلول الويب والتكامل البرمجي", dept: "دائرة البرمجة والتقنية", section: "قسم الويب", email: "b.qassem@eyasmedia.com", status: "نشط", avatar: "ب" },
    { id: 12, emp_code: "12", name: "رانيا أبو شعبان", role: "رئيسة قسم جودة وفحص البرمجيات QA", dept: "دائرة البرمجة والتقنية", section: "قسم التطبيقات", email: "r.shaban@eyasmedia.com", status: "نشط", avatar: "ر" },
    { id: 13, emp_code: "13", name: "يوسف الشوا", role: "مطور تطبيقات هواتف ذكية (Senior Flutter)", dept: "دائرة البرمجة والتقنية", section: "قسم التطبيقات", email: "y.shawa@eyasmedia.com", status: "نشط", avatar: "ي" },
    { id: 14, emp_code: "14", name: "طارق الهسي", role: "مطور تطبيقات أندرويد وNative iOS", dept: "دائرة البرمجة والتقنية", section: "قسم التطبيقات", email: "t.hissi@eyasmedia.com", status: "نشط", avatar: "ط" },
    { id: 15, emp_code: "15", name: "دينا الغصين", role: "مطورة واجهات تفاعلية لتطبيقات الهواتف", dept: "دائرة البرمجة والتقنية", section: "قسم التطبيقات", email: "d.ghussein@eyasmedia.com", status: "نشط", avatar: "د" },
    { id: 16, emp_code: "16", name: "مهند أبو حصيره", role: "مطور تطبيقات موبايل وخدمات API", dept: "دائرة البرمجة والتقنية", section: "قسم التطبيقات", email: "m.hasira@eyasmedia.com", status: "نشط", avatar: "م" },
    { id: 17, emp_code: "17", name: "ميساء الغصين", role: "كبيرة مهندسي قواعد البيانات (DBA Lead)", dept: "دائرة البرمجة والتقنية", section: "قسم قواعد البيانات", email: "m.ghussein.db@eyasmedia.com", status: "نشط", avatar: "م" },
    { id: 18, emp_code: "18", name: "حمزة الريس", role: "مهندس بنية تحتية وسحابية DevOps", dept: "دائرة البرمجة والتقنية", section: "قسم قواعد البيانات", email: "h.rayyes@eyasmedia.com", status: "نشط", avatar: "ح" },
    { id: 19, emp_code: "19", name: "خالد البورنو", role: "مسؤول أمن البيانات والنسخ الاحتياطي", dept: "دائرة البرمجة والتقنية", section: "قسم قواعد البيانات", email: "k.bourno@eyasmedia.com", status: "نشط", avatar: "خ" },
    { id: 20, emp_code: "20", name: "نور مشتهى", role: "محللة بيانات وإدارة مستودعات البيانات", dept: "دائرة البرمجة والتقنية", section: "قسم قواعد البيانات", email: "n.mushtaha@eyasmedia.com", status: "نشط", avatar: "ن" }
  ],

  // دائرة التسويق (15 موظفاً: من 21 إلى 35)
  marketing_dept: [
    { id: 21, emp_code: "21", name: "عمر العشي", role: "رئيس قسم التسويق الرقمي وإدارة القنوات", dept: "دائرة التسويق", section: "التسويق الرقمي", email: "o.ashi@eyasmedia.com", status: "نشط", avatar: "ع" },
    { id: 22, emp_code: "22", name: "هبة سكيك", role: "أخصائية تحسين محركات البحث والظهور SEO", dept: "دائرة التسويق", section: "التسويق الرقمي", email: "h.sukaik@eyasmedia.com", status: "نشط", avatar: "هـ" },
    { id: 23, emp_code: "23", name: "زياد مرتجى", role: "أخصائي كتابة وصناعة المحتوى التسويقي", dept: "دائرة التسويق", section: "التسويق الرقمي", email: "z.murtaja@eyasmedia.com", status: "نشط", avatar: "ز" },
    { id: 24, emp_code: "24", name: "لما رضوان", role: "أخصائية تسويق عبر منصات التواصل الاجتماعي", dept: "دائرة التسويق", section: "التسويق الرقمي", email: "l.radwan@eyasmedia.com", status: "نشط", avatar: "ل" },
    { id: 25, emp_code: "25", name: "فراس اليازجي", role: "محلل بيانات تسويقية وسلوك الجمهور", dept: "دائرة التسويق", section: "التسويق الرقمي", email: "f.yazji@eyasmedia.com", status: "نشط", avatar: "ف" },
    { id: 26, emp_code: "26", name: "كمال أبو رمضان", role: "رئيس قسم إدارة وتخطيط الحملات الإعلانية", dept: "دائرة التسويق", section: "إدارة الحملات", email: "k.ramadan@eyasmedia.com", status: "نشط", avatar: "ك" },
    { id: 27, emp_code: "27", name: "ريم العلمي", role: "منسقة الحملات الترويجية والإنتاجية", dept: "دائرة التسويق", section: "إدارة الحملات", email: "r.alami@eyasmedia.com", status: "نشط", avatar: "ر" },
    { id: 28, emp_code: "28", name: "وسيم كحيل", role: "أخصائي إدارة الإعلانات الرقمية الممولة Media Buyer", dept: "دائرة التسويق", section: "إدارة الحملات", email: "w.kuheil@eyasmedia.com", status: "نشط", avatar: "و" },
    { id: 29, emp_code: "29", name: "آلاء مهنا", role: "مصممة جرافيك متخصصة بالحملات الإعلانية", dept: "دائرة التسويق", section: "إدارة الحملات", email: "a.muhanna@eyasmedia.com", status: "نشط", avatar: "آ" },
    { id: 30, emp_code: "30", name: "تامر الشرفا", role: "مشرف إنتاج المواد الترويجية والوسائط", dept: "دائرة التسويق", section: "إدارة الحملات", email: "t.sharafa@eyasmedia.com", status: "نشط", avatar: "ت" },
    { id: 31, emp_code: "31", name: "ماجد صيام", role: "رئيس قسم العلاقات العامة والاتصال المؤسسي", dept: "دائرة التسويق", section: "العلاقات العامة", email: "m.siam@eyasmedia.com", status: "نشط", avatar: "م" },
    { id: 32, emp_code: "32", name: "نداء حلس", role: "أخصائية التشبيك والعلاقات الإعلامية", dept: "دائرة التسويق", section: "العلاقات العامة", email: "n.helles@eyasmedia.com", status: "نشط", avatar: "ن" },
    { id: 33, emp_code: "33", name: "سامح بكر", role: "منسق الفعاليات والمؤتمرات واللقاءات الإعلامية", dept: "دائرة التسويق", section: "العلاقات العامة", email: "s.bakr@eyasmedia.com", status: "نشط", avatar: "س" },
    { id: 34, emp_code: "34", name: "جمانة كراجة", role: "مسؤولة التواصل والشراكات المؤسسية", dept: "دائرة التسويق", section: "العلاقات العامة", email: "j.karaja@eyasmedia.com", status: "نشط", avatar: "ج" },
    { id: 35, emp_code: "35", name: "عادل دلول", role: "أخصائي الرصد الإعلامي وقياس الأثر المجتمعي", dept: "دائرة التسويق", section: "العلاقات العامة", email: "a.dalloul@eyasmedia.com", status: "نشط", avatar: "ع" }
  ]
};

// دمج القائمة الكاملة
window.ALL_EMPLOYEES = [
  ...window.EMS_DATA.leadership,
  ...window.EMS_DATA.media_dept,
  ...window.EMS_DATA.tech_dept,
  ...window.EMS_DATA.marketing_dept
];
