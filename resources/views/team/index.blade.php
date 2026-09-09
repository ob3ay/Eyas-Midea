@extends('layouts.portal')
@section('title', 'دليل فريق العمل')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">دليل فريق العمل</span>
            </div>
            <h1 class="ems-page-title">دليل كوادر وفريق عمل مجموعة إياس الإعلامية</h1>
            <p class="ems-page-subtitle">بطاقات تعريفية لكافة الكوادر من الرقم 1 إلى 35 برئاسة رئيس مجلس الإدارة</p>
          </div>
          <div class="ems-page-actions">
            <a href="employees.html" class="ems-btn ems-btn-outline">عرض جدول الموظفين ←</a>
          </div>
        </div>

        <!-- فلاتر التبويب -->
        <div class="ems-tabs" id="teamDeptTabs">
          <button class="ems-tab active" onclick="filterTeamDept('all', this)">الكل (36)</button>
          <button class="ems-tab" onclick="filterTeamDept('mgmt', this)">الإدارة العليا والتنفيذية (4)</button>
          <button class="ems-tab" onclick="filterTeamDept('media', this)">دائرة الإعلام (2)</button>
          <button class="ems-tab" onclick="filterTeamDept('tech', this)">دائرة البرمجة والتقنية (15)</button>
          <button class="ems-tab" onclick="filterTeamDept('mktg', this)">دائرة التسويق والعلاقات (15)</button>
        </div>

        <!-- شبكة بطاقات فريق العمل الكاملة -->
        <div class="ems-team-grid" id="teamCardsContainer">
          
          <!-- رئيس مجلس الإدارة -->
          <div class="ems-member-card" data-dept="mgmt" style="border: 2px solid var(--ems-gold); background: #fdfbf7;">
            <span class="ems-member-id-badge" style="background: var(--ems-gold); color: #fff;">رئيس المجلس</span>
            <div>
              <div class="ems-member-avatar-box" style="background: var(--ems-gold-gradient);">ص</div>
              <h3 class="ems-member-name">صالح رزق محمد حسنين</h3>
              <div class="ems-member-role">رئيس مجلس الإدارة (Super Admin)</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> مجلس الإدارة العليا</span>
              <span><strong>البريد:</strong> saleh@eyasmedia.com</span>
              <span class="ems-badge badge-gold" style="align-self: flex-start; margin-top: 6px;">Full Scope</span>
            </div>
          </div>

          <!-- 1. محمد ابو شحمه -->
          <div class="ems-member-card" data-dept="mgmt">
            <span class="ems-member-id-badge">الرقم الوظيفي: 1</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #1e3a8a;">م</div>
              <h3 class="ems-member-name">محمد ابو شحمه</h3>
              <div class="ems-member-role">المدير التنفيذي (CEO)</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> الإدارة العليا والتنفيذية</span>
              <span><strong>البريد:</strong> m.abushahma@eyasmedia.com</span>
              <span><strong>الهاتف:</strong> +970 59 900 0002</span>
            </div>
          </div>

          <!-- 2. براء عمر -->
          <div class="ems-member-card" data-dept="mgmt">
            <span class="ems-member-id-badge">الرقم الوظيفي: 2</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #0284c7;">ب</div>
              <h3 class="ems-member-name">براء عمر</h3>
              <div class="ems-member-role">المستشار القانوني ونائب المدير التنفيذي</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> الإدارة العليا والتنفيذية</span>
              <span><strong>البريد:</strong> b.omar@eyasmedia.com</span>
              <span><strong>الهاتف:</strong> +970 59 900 0003</span>
            </div>
          </div>

          <!-- 3. رواء صلاح -->
          <div class="ems-member-card" data-dept="mgmt">
            <span class="ems-member-id-badge">الرقم الوظيفي: 3</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #db2777;">ر</div>
              <h3 class="ems-member-name">رواء صلاح</h3>
              <div class="ems-member-role">السكرتارية التنفيذية ومتابعة المكاتب</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> الإدارة العليا والتنفيذية</span>
              <span><strong>البريد:</strong> r.salah@eyasmedia.com</span>
              <span><strong>الهاتف:</strong> +970 59 900 0004</span>
            </div>
          </div>

          <!-- 4. البراء ابو عامر -->
          <div class="ems-member-card" data-dept="media">
            <span class="ems-member-id-badge">الرقم الوظيفي: 4</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #2563eb;">ب</div>
              <h3 class="ems-member-name">البراء ابو عامر</h3>
              <div class="ems-member-role">دائرة الإعلام - قسم المرئي</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> دائرة الإعلام</span>
              <span><strong>البريد:</strong> b.abuamer@eyasmedia.com</span>
              <span><strong>الهاتف:</strong> +970 59 900 0005</span>
            </div>
          </div>

          <!-- 5. لؤي بركة -->
          <div class="ems-member-card" data-dept="media">
            <span class="ems-member-id-badge">الرقم الوظيفي: 5</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #0284c7;">ل</div>
              <h3 class="ems-member-name">لؤي بركة</h3>
              <div class="ems-member-role">دائرة الإعلام - منسق الرقمي</div>
            </div>
            <div class="ems-member-meta">
              <span><strong>الدائرة:</strong> دائرة الإعلام</span>
              <span><strong>البريد:</strong> l.baraka@eyasmedia.com</span>
              <span><strong>الهاتف:</strong> +970 59 900 0006</span>
            </div>
          </div>

          <!-- 6 إلى 20 دائرة البرمجة والتقنية -->
          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 6</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">أ</div>
              <h3 class="ems-member-name">أحمد حبيب</h3>
              <div class="ems-member-role">رئيس قسم الويب ومطور Full-Stack</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>a.habib@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 7</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">م</div>
              <h3 class="ems-member-name">محمود المصري</h3>
              <div class="ems-member-role">مهندس أنظمة خلفية (Senior Backend)</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>m.masri@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 8</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">إ</div>
              <h3 class="ems-member-name">إبراهيم الصالحي</h3>
              <div class="ems-member-role">مطور واجهات أمامية (Frontend)</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>i.salhi@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 9</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #8b5cf6;">س</div>
              <h3 class="ems-member-name">سارة الخزندار</h3>
              <div class="ems-member-role">مهندسة تصميم واجهات UI/UX</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>s.khazendar@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 10</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">أ</div>
              <h3 class="ems-member-name">أنس الدلو</h3>
              <div class="ems-member-role">مطور مكونات الويب الحديثة</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>a.dalou@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 11</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">ب</div>
              <h3 class="ems-member-name">بلال قاسم</h3>
              <div class="ems-member-role">مطور حلول الويب والتكامل البرمجي</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم الويب</span><span>b.qassem@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 12</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">ر</div>
              <h3 class="ems-member-name">رانيا أبو شعبان</h3>
              <div class="ems-member-role">رئيسة قسم جودة وفحص البرمجيات QA</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم التطبيقات</span><span>r.shaban@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 13</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #0284c7;">ي</div>
              <h3 class="ems-member-name">يوسف الشوا</h3>
              <div class="ems-member-role">مطور تطبيقات هواتف ذكية (Flutter)</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم التطبيقات</span><span>y.shawa@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 14</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #0284c7;">ط</div>
              <h3 class="ems-member-name">طارق الهسي</h3>
              <div class="ems-member-role">مطور تطبيقات أندرويد وiOS</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم التطبيقات</span><span>t.hissi@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 15</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #db2777;">د</div>
              <h3 class="ems-member-name">دينا الغصين</h3>
              <div class="ems-member-role">مطورة واجهات تفاعلية لتطبيقات الهواتف</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم التطبيقات</span><span>d.ghussein@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 16</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #15803d;">م</div>
              <h3 class="ems-member-name">مهند أبو حصيره</h3>
              <div class="ems-member-role">مطور تطبيقات موبايل وخدمات API</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قسم التطبيقات</span><span>m.hasira@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 17</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #059669;">م</div>
              <h3 class="ems-member-name">ميساء الغصين</h3>
              <div class="ems-member-role">كبيرة مهندسي قواعد البيانات (DBA Lead)</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قواعد البيانات</span><span>m.ghussein.db@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 18</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #059669;">ح</div>
              <h3 class="ems-member-name">حمزة الريس</h3>
              <div class="ems-member-role">مهندس بنية تحتية وسحابية DevOps</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قواعد البيانات</span><span>h.rayyes@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 19</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #059669;">خ</div>
              <h3 class="ems-member-name">خالد البورنو</h3>
              <div class="ems-member-role">مسؤول أمن البيانات والنسخ الاحتياطي</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قواعد البيانات</span><span>k.bourno@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="tech">
            <span class="ems-member-id-badge">الرقم: 20</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #059669;">ن</div>
              <h3 class="ems-member-name">نور مشتهى</h3>
              <div class="ems-member-role">محللة بيانات ومستودعات البيانات</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> البرمجة والتقنية • قواعد البيانات</span><span>n.mushtaha@eyasmedia.com</span></div>
          </div>

          <!-- 21 إلى 35 دائرة التسويق -->
          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 21</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #b45309;">ع</div>
              <h3 class="ems-member-name">عمر العشي</h3>
              <div class="ems-member-role">رئيس قسم التسويق الرقمي وإدارة القنوات</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • التسويق الرقمي</span><span>o.ashi@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 22</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #b45309;">هـ</div>
              <h3 class="ems-member-name">هبة سكيك</h3>
              <div class="ems-member-role">أخصائية تحسين محركات البحث SEO</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • التسويق الرقمي</span><span>h.sukaik@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 23</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #b45309;">ز</div>
              <h3 class="ems-member-name">زياد مرتجى</h3>
              <div class="ems-member-role">أخصائي كتابة وصناعة المحتوى التسويقي</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • التسويق الرقمي</span><span>z.murtaja@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 24</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #b45309;">ل</div>
              <h3 class="ems-member-name">لما رضوان</h3>
              <div class="ems-member-role">أخصائية منصات التواصل والإعلانات الرقمية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • التسويق الرقمي</span><span>l.radwan@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 25</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #b45309;">ف</div>
              <h3 class="ems-member-name">فراس اليازجي</h3>
              <div class="ems-member-role">محلل بيانات تسويقية وسلوك الجمهور</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • التسويق الرقمي</span><span>f.yazji@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 26</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">ك</div>
              <h3 class="ems-member-name">كمال أبو رمضان</h3>
              <div class="ems-member-role">رئيس قسم إدارة وتخطيط الحملات الإعلانية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • إدارة الحملات</span><span>k.ramadan@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 27</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">ر</div>
              <h3 class="ems-member-name">ريم العلمي</h3>
              <div class="ems-member-role">منسقة الحملات الترويجية والإنتاجية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • إدارة الحملات</span><span>r.alami@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 28</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">و</div>
              <h3 class="ems-member-name">وسيم كحيل</h3>
              <div class="ems-member-role">أخصائي إدارة الإعلانات الممولة Media Buyer</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • إدارة الحملات</span><span>w.kuheil@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 29</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">آ</div>
              <h3 class="ems-member-name">آلاء مهنا</h3>
              <div class="ems-member-role">مصممة جرافيك متخصصة بالحملات الإعلانية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • إدارة الحملات</span><span>a.muhanna@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 30</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #ea580c;">ت</div>
              <h3 class="ems-member-name">تامر الشرفا</h3>
              <div class="ems-member-role">مشرف إنتاج المواد الترويجية والوسائط</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • إدارة الحملات</span><span>t.sharafa@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 31</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #c2410c;">م</div>
              <h3 class="ems-member-name">ماجد صيام</h3>
              <div class="ems-member-role">رئيس قسم العلاقات العامة والاتصال المؤسسي</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • العلاقات العامة</span><span>m.siam@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 32</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #c2410c;">ن</div>
              <h3 class="ems-member-name">نداء حلس</h3>
              <div class="ems-member-role">أخصائية التشبيك والعلاقات الإعلامية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • العلاقات العامة</span><span>n.helles@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 33</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #c2410c;">س</div>
              <h3 class="ems-member-name">سامح بكر</h3>
              <div class="ems-member-role">منسق الفعاليات والمؤتمرات واللقاءات الإعلامية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • العلاقات العامة</span><span>s.bakr@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 34</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #c2410c;">ج</div>
              <h3 class="ems-member-name">جمانة كراجة</h3>
              <div class="ems-member-role">مسؤولة التواصل والشراكات المؤسسية</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • العلاقات العامة</span><span>j.karaja@eyasmedia.com</span></div>
          </div>

          <div class="ems-member-card" data-dept="mktg">
            <span class="ems-member-id-badge">الرقم: 35</span>
            <div>
              <div class="ems-member-avatar-box" style="background: #c2410c;">ع</div>
              <h3 class="ems-member-name">عادل دلول</h3>
              <div class="ems-member-role">أخصائي الرصد الإعلامي وقياس الأثر المجتمعي</div>
            </div>
            <div class="ems-member-meta"><span><strong>الدائرة:</strong> دائرة التسويق • العلاقات العامة</span><span>a.dalloul@eyasmedia.com</span></div>
          </div>

        </div>
      </div>
@endsection

