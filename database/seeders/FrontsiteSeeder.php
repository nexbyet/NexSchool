<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Notice;
use App\Models\SliderItem;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class FrontsiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pages
        $about = Page::create(['title_gu'=>'અમારા વિશે','title_en'=>'About Us','slug'=>'about-us','content_gu'=>'<p>અમારી શાળા વિશે માહિતી અહીં દર્શાવવામાં આવશે.</p>','content_en'=>'<p>Information about our school will be displayed here.</p>','meta_title'=>'અમારા વિશે - NexSchool','status'=>true]);
        $admission = Page::create(['title_gu'=>'પ્રવેશ','title_en'=>'Admission','slug'=>'admission','content_gu'=>'<p>પ્રવેશ પ્રક્રિયા અને માપદંડ અહીં દર્શાવવામાં આવશે.</p>','content_en'=>'<p>Admission process and criteria will be displayed here.</p>','meta_title'=>'પ્રવેશ - NexSchool','status'=>true]);
        $gallery = Page::create(['title_gu'=>'ગેલેરી','title_en'=>'Gallery','slug'=>'gallery','content_gu'=>'<p>શાળાની ફોટો ગેલેરી.</p>','content_en'=>'<p>School photo gallery.</p>','meta_title'=>'ગેલેરી - NexSchool','status'=>true]);
        $contact = Page::create(['title_gu'=>'સંપર્ક','title_en'=>'Contact','slug'=>'contact','content_gu'=>'<p>અમારો સંપર્ક કરવા માટે નીચેની વિગતોનો ઉપયોગ કરો.</p>','content_en'=>'<p>Use the details below to contact us.</p>','meta_title'=>'સંપર્ક - NexSchool','status'=>true]);

        // 2. Header Menu
        $headerMenu = Menu::create(['name'=>'મુખ્ય મેનુ','location'=>'header','status'=>true]);
        $homeItem = MenuItem::create(['menu_id'=>$headerMenu->id,'title_gu'=>'હોમ','title_en'=>'Home','url'=>'/','sort_order'=>1,'status'=>true]);
        $aboutItem = MenuItem::create(['menu_id'=>$headerMenu->id,'title_gu'=>'અમારા વિશે','title_en'=>'About Us','page_id'=>$about->id,'sort_order'=>2,'status'=>true]);
        MenuItem::create(['menu_id'=>$headerMenu->id,'parent_id'=>$aboutItem->id,'title_gu'=>'અમારું મિશન','title_en'=>'Our Mission','url'=>'/about-us#mission','sort_order'=>1,'status'=>true]);
        MenuItem::create(['menu_id'=>$headerMenu->id,'parent_id'=>$aboutItem->id,'title_gu'=>'અમારી ટીમ','title_en'=>'Our Team','url'=>'/about-us#team','sort_order'=>2,'status'=>true]);
        MenuItem::create(['menu_id'=>$headerMenu->id,'title_gu'=>'પ્રવેશ','title_en'=>'Admission','page_id'=>$admission->id,'sort_order'=>3,'status'=>true]);
        MenuItem::create(['menu_id'=>$headerMenu->id,'title_gu'=>'ગેલેરી','title_en'=>'Gallery','page_id'=>$gallery->id,'sort_order'=>4,'status'=>true]);
        MenuItem::create(['menu_id'=>$headerMenu->id,'title_gu'=>'સંપર્ક','title_en'=>'Contact','page_id'=>$contact->id,'sort_order'=>5,'status'=>true]);

        // 3. Footer Menu
        Menu::create(['name'=>'ફૂટર મેનુ','location'=>'footer','status'=>true]);

        // 4. Slider Items
        SliderItem::create(['title_gu'=>'શાળામાં આપનું સ્વાગત છે','title_en'=>'Welcome to Our School','subtitle_gu'=>'ઉત્તમ શિક્ષણ અને સર્વાંગી વિકાસ','subtitle_en'=>'Excellence in Education','link_url'=>'/about-us','sort_order'=>1,'status'=>true]);
        SliderItem::create(['title_gu'=>'પ્રવેશ શરૂ','title_en'=>'Admissions Open','subtitle_gu'=>'નવા શૈક્ષણિક વર્ષ માટે પ્રવેશ ચાલુ','subtitle_en'=>'Enroll for the new academic year','link_url'=>'/admission','sort_order'=>2,'status'=>true]);
        SliderItem::create(['title_gu'=>'અમારી સિદ્ધિઓ','title_en'=>'Our Achievements','subtitle_gu'=>'શિક્ષણ ક્ષેત્રે ઉત્કૃષ્ટ પ્રદર્શન','subtitle_en'=>'Outstanding performance in academics','sort_order'=>3,'status'=>true]);

        // 5. Notices
        Notice::create(['title_gu'=>'નવા શૈક્ષણિક વર્ષની જાહેરાત','title_en'=>'New Academic Year Announcement','content_gu'=>'નવું શૈક્ષણિક વર્ષ ૧ જૂનથી શરૂ થશે.','content_en'=>'The new academic year will begin from June 1st.','date'=>now(),'is_circular'=>true,'status'=>true]);
        Notice::create(['title_gu'=>'ઉનાળુ રજાની જાહેરાત','title_en'=>'Summer Vacation Notice','content_gu'=>'ઉનાળુ રજા ૧ મેથી ૧૫ જૂન સુધી રહેશે.','content_en'=>'Summer vacation will be from May 1 to June 15.','date'=>now(),'is_circular'=>false,'status'=>true]);

        // 6. Gallery
        $galleryModel = Gallery::create(['name_gu'=>'શાળા ગેલેરી','name_en'=>'School Gallery','description_gu'=>'શાળાની યાદગાર ક્ષણો','description_en'=>'Memorable moments of the school','sort_order'=>1,'status'=>true]);
        GalleryImage::create(['gallery_id'=>$galleryModel->id,'caption_gu'=>'શાળા મકાન','caption_en'=>'School Building','image'=>'','sort_order'=>1]);
        GalleryImage::create(['gallery_id'=>$galleryModel->id,'caption_gu'=>'વર્ગખંડ','caption_en'=>'Classroom','image'=>'','sort_order'=>2]);
        GalleryImage::create(['gallery_id'=>$galleryModel->id,'caption_gu'=>'પ્રાર્થના','caption_en'=>'Prayer Assembly','image'=>'','sort_order'=>3]);

        // 7. Homepage Sections
        $order = 1;
        HomepageSection::create(['type'=>'notice_ticker','sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'slider','sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'about','content'=>['title_gu'=>'અમારી શાળા','description_gu'=>'અમારી શાળા ગુણવત્તાયુક્ત શિક્ષણ પ્રદાન કરવા માટે સમર્પિત છે. અમે વિદ્યાર્થીઓના સર્વાંગી વિકાસ માટે પ્રતિબદ્ધ છીએ.','image'=>''],'sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'features','content'=>['title_gu'=>'અમારી વિશેષતાઓ','subtitle_gu'=>'શા માટે અમારી શાળા પસંદ કરવી?','items'=>[['icon'=>'lni lni-book-1','title_gu'=>'ઉત્તમ શિક્ષણ','description_gu'=>'અનુભવી શિક્ષકો દ્વારા ગુણવત્તાયુક્ત શિક્ષણ'],['icon'=>'lni lni-laptop-2','title_gu'=>'આધુનિક સુવિધાઓ','description_gu'=>'કમ્પ્યુટર લેબ, સ્માર્ટ ક્લાસરૂમ'],['icon'=>'lni lni-user-multiple-4','title_gu'=>'સર્વાંગી વિકાસ','description_gu'=>'રમતગમત, સાંસ્કૃતિક પ્રવૃત્તિઓ'],['icon'=>'lni lni-books-2','title_gu'=>'સમૃદ્ધ પુસ્તકાલય','description_gu'=>'૫૦૦૦+ પુસ્તકો સાથે પુસ્તકાલય']]],'sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'stats','content'=>['title_gu'=>'અમારી સિદ્ધિઓ','stats'=>[['number'=>'500+','label_gu'=>'વિદ્યાર્થીઓ'],['number'=>'30+','label_gu'=>'શિક્ષકો'],['number'=>'15+','label_gu'=>'વર્ગખંડો'],['number'=>'10+','label_gu'=>'પુરસ્કારો']]],'sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'gallery','sort_order'=>$order++,'status'=>true]);
        HomepageSection::create(['type'=>'contact','content'=>['title_gu'=>'સંપર્ક કરો','subtitle_gu'=>'અમારો સંપર્ક કરવા માટે નીચેની વિગતોનો ઉપયોગ કરો','map_embed'=>''],'sort_order'=>$order++,'status'=>true]);
    }
}
