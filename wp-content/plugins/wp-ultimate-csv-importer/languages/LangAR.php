<?php
/**
 * WP Ultimate CSV Importer plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\FCSV;

if (!defined('ABSPATH'))
        exit; // Exit if accessed directly

class LangAR
{
        private static $arabic_instance = null, $media_instance;

        public static function getInstance()
        {
                if (LangAR::$arabic_instance == null) {
                        LangAR::$arabic_instance = new LangAR;
                        return LangAR::$arabic_instance;
                }
                return LangAR::$arabic_instance;
        }

        public static function contents()
        {
                $response = array(
					'Exporterwithadvancedfilters' => 'مصدر بفلاتر متقدمة',
					'Buynow' => 'اشتري الآن!',
					'Exportfiltereddata' => 'تصدير البيانات المفلترة',
                	'Exportfiltereddatadesc' => 'يتيح لك الحصول على البيانات المطلوبة فقط باستخدام عوامل التصفية المتقدمة المختلفة',
                	'Backupineditableformat' => 'نسخة احتياطية في شكل قابل للتحرير',
                	'Backupineditableformatdesc' => 'النسخ الاحتياطي في 4 تنسيقات ملفات مختلفة مثلCSV, XML, JSON,XLS.',
                	'AutoScheduledBackups' => 'النسخ الاحتياطي التلقائي المجدولة',
                	'AutoScheduledBackupsdesc' => 'يساعد التصدير المجدول في النسخ الاحتياطي كتنسيق ملف نصي قابل للتحرير في فاصل زمني منتظم.',
					'Updateolderpostsfromsingleimport' => 'تحديث المنشورات القديمة من استيراد واحد',
					'JetEngineMetaboxToolsetTypesACFproFreeandPodsFieldPostPluginsImporter' => 
                	'JetEngine, Metabox, Toolset Types, ACF pro / Free and Pods Field/Post مستورد الإضافات',
					'AutoSchedulewithreusabletemplates' => 'جدولة تلقائية مع قوالب قابلة لإعادة الاستخدام',
					'WPMLImporter' => 'مستورد WPML',
					'SEOPluginsDataImporterRankMathYoastandAllinOneSEO' => 'مستورد بيانات إضافات تحسين محركات البحث (SEO) - RankMath, SEOPress, Yoast and All in One SEO',
					'AIOWooCommerceImportSuit' => 'بدلة استيراد AIO WooCommerce',
					'ImportUpdate' => 'استيراد التحديث',
					'Dashboard' => 'لوحة القيادة',
					'Manager' => 'مدير',
					'Export' => 'يصدّر',
					'Settings' => 'إعدادات',
					'Support' => 'الدعم',
					'UploadfromDesktop' => 'تحميل من سطح المكتب',
					'UploadfromFTPSFTP' => 'تحميل من FTPSFTP',
					'UploadfromFTP' => 'تحميل من FTPSFTP',
					'UploadfromURL' => 'تحميل من عنوان URL',
					'ChoosFileintheServer' => 'اختيارات FileintheServer',
					'Drag&Dropyourfilesor' => 'سحب وإسقاط الملفات أو',
					'SIMPLEMODE' => 'الوضع البسيط',
					'Browse' => 'تصفح',
					'NewItem' => 'عنصر جديد',
					'ExistingItems' => 'العناصر الموجودة',
					'ImportEachRecordAs' => 'ImportEachRecordAs',
					'Continue' => 'يكمل',
					'Search' => 'يبحث',
					'FromDate' => 'من التاريخ',
					'ToDate' => 'حتي اليوم',
					'SEARCH' => 'بحث',
					'SavedTemplate' => 'SavedTemplate',
					'TEMPLATES' => 'القوالب',
					'MATCHEDCOLUMNSCOUNT' => 'مطابقة العدد',
					'MODULE' => 'وحدة',
					'CREATEDTIME' => 'وقت الإنشاء',
					'ACTION' => 'عمل',
					'USETEMPLATE' => 'استخدم القالب',
					'CREATENEWMAPPING' => 'إنشاء الخرائط',
					'BACK' => 'الى الخلف',
					'ADVANCEDMODE' => 'وضع متقدم',
					'DRAGDROPMODE' => 'DRAGDROPMODE',
					'WordpressFields' => 'WordpressFields',
					'WPFIELDS' => 'WPFIELDS',
					'CSVHEADER' => 'CSVHEADER',
					'Action' => 'عمل',
					'Name' => 'اسم',
					'HINT' => 'ملحوظة',
					'Example' => 'مثال',
					'WordPressCoreFields' => 'WordPressCoreFields',
					'ACFFreeFields' => 'حقول ACFFree',
					'ACFFields' => 'حقول ACF',
					'ACFGroupFields' => 'حقول ACFGroup',
					'ACFProFields' => 'حقول ACFProFields',
					'ACFRepeaterFields' => 'ACF مكرر الحقول',
					'TypesCustomFields' => 'أنواع الحقول المخصصة',
					'PodsFields' => 'PodsFields',
					'JobListingFields' => 'JobListingFields',
					'CustomFieldSuite' => 'CustomFieldSuite',
					'AllInOneSeoFields' => 'AllInOneSeoFields',
					'MetaBoxFields' => 'حقول ميتابوكس',
					'YoastSeoFields' => 'YoastSeoFields',
					'RankMathFields' => 'RankMathFields',
					'RankMathProFields' => 'RankMathProFields',
					'BillingAndShippingInformation' => 'معلومات الفوترة والشحن',
					'CustomFieldsWPMemberFields' => 'الحقول المخصصة',
					'CustomFieldsMemberFields' => 'الحقول المخصصة',
					'ProductMetaFields' => 'المنتج',
					'ProductAttrFields' => 'ProductAttrFields',
					'ProductBundleMetaFields' => 'الحقول الوصفية لحزمة المنتج',
					'OrderMetaFields' => 'OrderMetaFields',
					'CouponMetaFields' => 'كوبون ميتافيلدز',
					'RefundMetaFields' => 'استرداد الأموال',
					'WPECommerceCustomFields' => 'WPECommerceCustomFields',
					'EventsManagerFields' => 'EventsManagerFields',
					'WPMLFields' => 'WPMLFields',
					'CMB2CustomFields' => 'حقول CMB2CustomFields',
					'JetEngineFields' => 'JetEngineFields',
					'JetEngineRFFields' => 'JetEngineRFFields',
					'JetEngineCPTFields' => 'JetEngineCPTFields',
					'JetEngineCPTRFFields' => 'JetEngineCPTRF الحقول',
					'JetEngineTaxonomyFields' => 'JetEngineTaxonomyFields',
					'JetEngineTaxonomyRFFields' => 'JetEngineTaxonomyRF الحقول',
					'JetEngineRelationsFields' => 'JetEngineRelationsFields',
					'CourseSettingsFields' => 'إعدادات المقرر',
					'CurriculumSettingsFields' => 'المناهج الدراسية',
					'QuizSettingsFields' => 'QuizSettingsFields',
					'LessonSettingsFields' => 'إعدادات الدرس الحقول',
					'QuestionSettingsFields' => 'أسئلة الإعدادات الحقول',
					'OrderSettingsFields' => 'OrderSettingsFields',
					'WordPressCustomFields' => 'WordPressCustomFields',
					'TermsandTaxonomies' => 'الشروط والضرائب',
					'IsSerialized' => 'متسلسل',
					'NoCustomFieldsFound' => 'NoCustomFieldsFound',
					'MediaUploadFields' => 'MediaUploadFields',
					'UploadMedia' => 'تحميل الوسائط',
					'UploadedListofFiles' => 'UploadedListofFiles',
					'UploadedMediaFileLists' => 'UploadedMediaFileLists',
					'SavethismappingasTemplate' => 'SavethismappingasTemplate',
					'Save' => 'يحفظ',
					'Doyouneedtoupdatethecurrentmapping' => 'يرجى الاطلاع على الخرائط الحالية',
					'Savethecurrentmappingasnewtemplate' => 'حفظ الخرائط الحالية كنموذج جديد',
					'Back' => 'خلف',
					'Size' => 'بحجم',
					'MediaHandling' => 'التعامل مع وسائل الإعلام',
					'Downloadexternalimagestoyourmedia' => 'تنزيل الصور الخارجية على الوسائط',
					'ImageHandling' => 'إيماج هاندلينج',
					'Usemediaimagesifalreadyavailable' => 'صور مفيدة متاحة بالفعل',
					'Doyouwanttooverwritetheexistingimages' => 'تريد الكتابة فوق الصور الموجودة',
					'ImageSizes' => 'أحجام الصور',
					'Thumbnail' => 'ظفري',
					'Medium' => 'متوسط',
					'MediumLarge' => 'متوسطة: كبيرة',
					'Large' => 'كبير',
					'Custom' => 'العادة',
					'Slug' => 'سبيكة',
					'Width' => 'عرض',
					'Height' => 'ارتفاع',
					'Addcustomsizes' => 'إضافة تخصيص',
					'PostContentImageOption' => 'PostContentImageOption',
					'DownloadPostContentExternalImagestoMedia' => 'DownloadPostContentExternalImagestoMedia',
					'MediaSEOAdvancedOptions' => 'MediaSEOAdvancedOptions',
					'polylangfields' => 'polylangfields',
					'SetimageTitle' => 'SetimageTitle',
					'SetimageCaption' => 'التسمية التوضيحية',
					'SetimageAltText' => 'SetimageAltText',
					'SetimageDescription' => 'الوصف',
					'Changeimagefilenameto' => 'Changeimagefilenameto',
					'ImportconfigurationSection' => 'قسم تكوين الاستيراد',
					'EnablesafeprestateRollback' => 'تمكن من حفظ ملفات إعادة الاسترجاع',
					'Backupbeforeimport' => 'قبل الاستيراد',
					'DoyouwanttoSWITCHONMaintenancemodewhileimport' => 'هل تريد أن تسويتها؟',
					'Doyouwanttohandletheduplicateonexistingrecords' => 'تريد التعامل مع السجلات المكررة الموجودة',
					'Mentionthefieldswhichyouwanttohandleduplicates' => 'اذكر الحقول التي تريدها للتعامل مع المضاعفات',
					'DoyouwanttoUpdateanexistingrecords' => 'هل تريد تحديث السجلات الموجودة',
					'Updaterecordsbasedon' => 'Updaterecordsbasedon',
					'DeletedatafromWordPress' => 'البيانات المحذوفة من WordPress',
					'EnabletodeletetheitemsnotpresentinCSVXMLfile' => 'تمكين الحذف العناصر غير الموجودة في ملف CSVXML',
					'DoyouwanttoSchedulethisImport' => 'Doyouwantto جدولة هذا الاستيراد',
					'ScheduleDate' => 'تاريخ الجدول الزمني',
					'ScheduleFrequency' => 'الجدول الزمني',
					'TimeZone' => 'وحدة زمنية',
					'ScheduleTime' => 'الجدول الزمني',
					'Schedule' => 'برنامج',
					'Import' => 'يستورد',
					'Format' => 'شكل',
					'OneTime' => 'مره واحده',
					'Daily' => 'اليومي',
					'Weekly' => 'أسبوعي',
					'Monthly' => 'شهريا',
					'Hourly' => 'ساعيا',
					'Every30mins' => 'كل 30 دقيقة',
					'Every15mins' => 'كل 15 دقيقة',
					'Every10mins' => 'كل 10 دقائق',
					'Every5mins' => 'كل 5 دقائق',
					'FileName' => 'اسم الملف',
					'FileSize' => 'حجم الملف',
					'Process' => 'معالجة',
					'Totalnoofrecords' => 'Totalnoofrecords',
					'CurrentProcessingRecord' => 'جاري تجهيز السجل',
					'RemainingRecord' => 'السجل المتبقي',
					'Completed' => 'مكتمل',
					'TimeElapsed' => 'الوقت المنقضي',
					'approximate' => 'تقريبي',
					'DownloadLog' => 'DownloadLog',
					'NoRecord' => 'لا سجلات',
					'UploadedCSVFileLists' => 'UploadedCSVFileLists',
					'Hostname' => 'اسم المضيف',
					'HostPort' => 'استضافة الميناء',
					'HostUsername' => 'اسم مستخدم المضيف',
					'HostPassword' => 'HostPassword',
					'HostPath' => 'هوستباث',
					'DefaultPort' => 'منفذ افتراضي',
					'FTPUsername' => 'اسم مستخدم FTP',
					'FTPPassword' => 'كلمة مرور FTP',
					'ConnectionType' => 'نوع الاتصال',
					'ImportersActivity' => 'نشاط المستوردين',
					'ImportStatistics' => 'استيراد الإحصائيات',
					'FileManager' => 'مدير الملفات',
					'SmartSchedule' => 'الجدول الذكي',
					'ScheduledExport' => 'مجدولة التصدير',
					'Templates' => 'القوالب',
					'LogManager' => 'مدير السجل',
					'NotSelectedAnyTab' => 'NotSelectedAnyTab',
					'EventInfo' => 'EventInfo',
					'EventDate' => 'تاريخ الحدث',
					'EventStatus' => 'EventStatus',
					'Actions' => 'أجراءات',
					'Date' => 'تاريخ',
					'Purpose' => 'غاية',
					'Revision' => 'مراجعة',
					'Select' => 'يختار',
					'Inserted' => 'تم إدراجها',
					'Updated' => 'محدث',
					'Skipped' => 'تم تخطي',
					'Delete' => 'حذف',
					'Noeventsfound' => 'Noeventsfound',
					'ScheduleInfo' => 'معلومات الجدول',
					'ScheduledDate' => 'التاريخ المجدول',
					'ScheduledTime' => 'جدول زمني',
					'Youhavenotscheduledanyevent' => 'Youhavenotscheduled أي حدث',
					'Frequency' => 'تكرار',
					'Time' => 'زمن',
					'EditSchedule' => 'تحرير الجدول',
					'SaveChanges' => 'SaveChanges',
					'TemplateInfo' => 'TemplateInfo',
					'TemplateName' => 'اسم القالب',
					'Module' => 'وحدة',
					'CreatedTime' => 'CreatedTime',
					'NoTemplateFound' => 'تم العثور على قالب',
					'Download' => 'تحميل',
					'NoLogRecordFound' => 'NoLogRecord تم العثور عليها',
					'GeneralSettings' => 'الاعدادات العامة',
					'DatabaseOptimization' => 'تحسين قاعدة البيانات',
					'SecurityandPerformance' => 'الأمان والأداء',
					'Documentation' => 'توثيق',
					'MediaReport' => 'وسائل الإعلام',
					'DropTable' => 'DropTable',
					'Ifenabledplugindeactivationwillremoveplugindatathiscannotberestored' => 'إذا تم تمكينه ، لا يمكن إعادة تنشيط التوصيل',
					'Scheduledlogmails' => 'المجدولة',
					'Enabletogetscheduledlogmails' => 'Enabletogetscheduledlogmails',
					'Sendpasswordtouser' => 'إرسال كلمة المرور',
					'Enabletosendpasswordinformationthroughemail' => 'التمكين من إرسال معلومات كلمة المرور',
					'WoocommerceCustomattribute' => 'السمة المخصصة',
					'Enablestoregisterwoocommercecustomattribute' => 'تمكين السجلالتاجر السمة المخصصة',
					'PleasemakesurethatyoutakenecessarybackupbeforeproceedingwithdatabaseoptimizationThedatalostcantbereverted' => 'نود التأكيد على أنه يتم إجراء نسخ احتياطي ضروري قبل المتابعة مع تحسين قاعدة البيانات',
					'DeleteallorphanedPostPageMeta' => 'DeleteallorphanedPostPageMeta',
					'Deleteallunassignedtags' => 'حذف العلامات غير المعينة',
					'DeleteallPostPagerevisions' => 'DeleteallPostPagerevisions',
					'DeleteallautodraftedPostPage' => 'DeleteallautodraftedPostPage',
					'DeleteallPostPageintrash' => 'DeleteallPostPageintrash',
					'DeleteallCommentsintrash' => 'حذف كافة التعليقات',
					'DeleteallUnapprovedComments' => 'Deleteall التعليقات غير المعتمدة',
					'DeleteallPingbackComments' => 'DeleteallPingback تعليقات',
					'DeleteallTrackbackComments' => 'Deleteall تتبع التعليقات',
					'DeleteallSpamComments' => 'حذف التعليقات غير المرغوب فيها',
					'RunDBOptimizer' => 'RunDBOptimizer',
					'DatabaseOptimizationLog' => 'سجل تحسين قاعدة البيانات',
					'noofOrphanedPostPagemetahasbeenremoved' => 'تم حذف علامة noofOrphanedPostPagemeta',
					'noofUnassignedtagshasbeenremoved' => 'تم حذف العلامات noofUnassignedtags',
					'noofPostPagerevisionhasbeenremoved' => 'تم حذف noofPostPagerevision',
					'noofAutodraftedPostPagehasbeenremoved' => 'تمت إزالة noofAutodraftedPostPage',
					'noofPostPageintrashhasbeenremoved' => 'تمت إزالة noofPostPageintrashhasbeen',
					'noofSpamcommentshasbeenremoved' => 'تمت إزالة التعليقات غير المرغوب فيها',
					'noofCommentsintrashhasbeenremoved' => 'تمت إزالة التعليقات',
					'noofUnapprovedcommentshasbeenremoved' => 'تمت إزالة التعليقات غير المعتمدة',
					'noofPingbackcommentshasbeenremoved' => 'تمت إزالة تعليقات بينغباك',
					'noofTrackbackcommentshasbeenremoved' => 'تمت إزالة التعليقات التراجعية',
					'Allowauthorseditorstoimport' => 'Allowauthorseditorstoimport',
					'Allowauthorseditorstoimport' => 'Allowauthorseditorstoimport',
					'Thisenablesauthorseditorstoimport' => 'هذا ممكن من المؤلفين المحررون الاستيراد',
					'MinimumrequiredphpinivaluesIniconfiguredvalues' => 'الحد الأدنى المطلوب من الدبابيس القيم المكونة الرمز',
					'Variables' => 'المتغيرات',
					'SystemValues' => 'قيم النظام',
					'MinimumRequirements' => 'الحد الأدنى من المتطلبات',
					'RequiredtoenabledisableLoadersExtentionsandmodules' => 'مطلوب للتمكينإضافات اللوادر والوحدات',
					'DebugInformation' => 'معلومات التصحيح',
					'SmackcodersGuidelines' => 'Smackcoders',
					'DevelopmentNews' => 'أخبار التنمية',
					'WhatsNew' => 'ما هو الجديد',
					'YoutubeChannel' => 'قناة يوتيوب',
					'OtherWordPressPlugins' => 'OtherWordPressPlugins',
					'Count' => 'عدد',
					'ImageType' => 'نوع الصورة',
					'Status' => 'حالة',
					'Loading' => 'جار التحميل',
					'LoveWPUltimateCSVImporterGivea5starreviewon' => 'LoveWPUltimateCSVImporterGivea5starreviewon',
					'ContactSupport' => 'اتصل بالدعم',
					'Email' => 'البريد الإلكتروني',
					'Supporttype' => 'نوع الدعم',
					'BugReporting' => 'تقرير الشوائب',
					'FeatureEnhancement' => 'ميزة التحسين',
					'Message' => 'رسالة',
					'Send' => 'إرسال',
					'NewsletterSubscription' => 'الاشتراك في النشرة الإخبارية',
					'Subscribe' => 'الإشتراك',
					'Note' => 'ملحوظة',
					'SubscribetoSmackcodersMailinglistafewmessagesayear' => 'الاشتراك في الرسائل النصية',
					'Pleasedraftamailto' => 'Pleasedraftamailto',
					'Ifyoudoesnotgetanyacknowledgementwithinanhour' => 'إذا لم تحصل على أي إعلان معترف به في غضون ساعة',
					'Selectyourmoduletoexportthedata' => 'حدد النموذج الخاص بك للتصدير البيانات',
					'Toexportdatabasedonthefilters' => 'قاعدة بيانات التصدير على أساس المرشحات',
					'ExportFileName' => 'ExportFileName',
					'AdvancedSettings' => 'إعدادات متقدمة',
					'ExportType' => 'نوع التصدير',
					'SplittheRecord' => 'سجل سبليت',
					'AdvancedFilters' => 'مرشحات متقدمة',
					'Exportdatawithautodelimiters' => 'تصدير البيانات في محددات السيارات',
					'Delimiters' => 'المحددات',
					'OtherDelimiters' => 'المطهرات الأخرى',
					'Exportdataforthespecificperiod' => 'تصدير بيانات ذات فترة محددة',
					'StartFrom' => 'يبدأ من',
					'EndTo' => 'نهاية ل',
					'Exportdatawiththespecificstatus' => 'تصدير البيانات مع الوضع الخاص',
					'All' => 'الجميع',
					'Publish' => 'ينشر',
					'Sticky' => 'لزج',
					'Private' => 'خاص',
					'Protected' => 'محمي',
					'Draft' => 'مسودة',
					'Pending' => 'قيد الانتظار',
					'Exportdatabyspecificauthors' => 'تصدير بيانات المؤلفين المحددين',
					'Authors' => 'المؤلفون',
					'ExportdatabasedonspecificInclusions' => 'تصدير البيانات المستندة إلى شوائب محددة',
					'DoyouwanttoSchedulethisExport' => 'الجدول الزمني للتصدير',
					'SelectTimeZone' => 'اختر المجال الزمني',
					'ScheduleExport' => 'الجدول الزمني',
					'DataExported' => 'تصدير البيانات',
					'FilePath' => 'مسار الملف',
					'Thisfeatureisavailablein' => 'هذه الميزة متوفرة في',
					'WPUltimateCSVImporter' => 'WP Ultimate CSV Importer',
					'PremiumVersion' => 'الإصدار المتميز',
					'ContactusforPresaleEnquiry' => 'اتصل بنا للحصول على الاستفسار قبل البيع',
					'importwoocommerce' => 'استيراد woocommerce',
					'ImportanybulkWooCommerceProductsdatainCSV' => 'قم باستيراد أي بيانات مجمعة لمنتجات WooCommerce في ملف CSV',
					'Highlights' => 'يسلط الضوء',
					'ProductTypessimplegroupedvariableexternaltypeimport' => '',
					'FeaturedProductImportfromURL' => 'استيراد منتج مميز من URL',
					'Galleryimageimport' => 'استيراد صور المعرض',
					'Duplicatedetection' => 'كشف مكرر',
					'FileType' => 'نوع الملف',
					'SupportsUTF_8CSVfile' => 'يدعم ملف UTF-8 CSV',
					'AlreadyInstalled' => 'مثبت مسبقا',
					'Install' => 'ثَبَّتَ',
					'ImportUsers' => 'استيراد المستخدمين',
					'ImportUserinfointoWordPressinbulk' => 'استيراد معلومات المستخدم إلى WordPress بكميات كبيرة',
					'WPMembersaddonsupport' => 'دعم إضافة أعضاء WP',
					'Defaultcustomfieldsimport' => 'استيراد الحقول المخصصة الافتراضية',
					'Sendsautomatedpasswordnotificationemailoptional' => 'يرسل بريدًا إلكترونيًا لإشعار كلمة المرور تلقائيًا (اختياري)',
					'WPUltimateExporter' => 'WP Ultimate Exporter',
					'ExportallyourWordPressdataasCSVfileforbackup' => 'قم بتصدير جميع بيانات WordPress الخاصة بك كملف CSV للنسخ الاحتياطي',
					'Supportsdefaultcustomfields' => 'يدعم الحقول المخصصة الافتراضية',
					'UTF8encodedCSVfile' => 'ملف CSV بترميز UTF-8',
					'SupportPostPageCustomPost' => 'دعم المنشور والصفحة والمنشور المخصص',
					'Filteredexportbasedonperiodoftimeauthors' => 'تصدير تمت تصفيته بناءً على الفترة الزمنية والمؤلفين',
					'Addons' => 'الإضافات',
					'Posts' => 'دعامات',
					'CustomPosts' => 'المشاركات المخصصة',
					'PostTags' => 'نشر العلامات',
					'PostCategories' => 'فئات المشاركة',
					'Users' => 'المستخدمون',
					'Taxonomies' => 'التصنيفات',
					'Comments' => 'تعليقات',
					'CustomerReviews' => 'آراء العملاء',
					'WooCommerceCoupons' => 'كوبونات WooCommerce',
					'WooCommerceRefunds' => 'المبالغ المستردة WooCommerce',
					'WooCommerceVariations' => 'الاختلافات في WooCommerce',
					'Found' => 'وجد',
					'CreateTopic' => 'إنشاء موضوع',
					'Createasupport' => 'قم بإنشاء موضوع دعم هنا للمساعدة',
					'Learnfrom' => 'تعلم من منشورات مدونتنا',
					'TechnicalDocumentation' => 'التوثيق الفني',
					'Getsampleandexamplefiles' => 'احصل على نماذج وأمثلة للملفات',
					'PleaseinstalltheUltimateExportertoexportallyourWordPressdataasCSV' => 'يرجى تثبيت Ultimate Exporter لتصدير جميع بيانات WordPress الخاصة بك بتنسيق CSV',
					'Clickheretoinstall' => 'انقر هنا للتثبيت',
					'Hire_us' => 'استئجار لنا',
					'GetSupport' => 'احصل على الدعم',
					'SampleCSVXML' => 'نموذج CSV & XML',
					'WarningImportforsomedataaredisabledInstallandactivatebelowpluginsfirst' => 'تحذير: بعض الإضافات مفقودة ، فمن المستحسن',
                );
                return $response;
        }
}

