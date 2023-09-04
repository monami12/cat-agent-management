<?php

namespace App\Http\Controllers;

use App\Models\Cat;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Illuminate\Contracts\Cache\Store;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Intervention\Image\ImageManager;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\PageBreak;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class CatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('cats.index', [
            'cats' => Cat::with('user')->latest()->get(),
        ]);
    }

    /**
     * 猫新規作成用ページ
     */
    public function create(): View
    {
        $api_key = env("CAT_API_KEY");

        //品種一覧取得
        $breeds = Http::get("https://api.thecatapi.com/v1/breeds?api_key={$api_key}")->json();
        if(count($breeds) == 0){
            return view('cats.create');
        }
        $breed = $breeds[rand(0,count($breeds)-1)];
        //dd($breed);

        $random_breed = $breed["id"];

        //猫情報取得
        $cats_data = Http::get("https://api.thecatapi.com/v1/images/search?{$api_key}&breed_ids={$random_breed}")->json();
        if(count($cats_data) == 0){
            return view('cats.create');
        }
        $cat_data = $cats_data[0];
        //dd($cat_data);

        //ランダムプロフィール作成
        $user = Http::get("https://randomuser.me/api/")->json()["results"][0];
        //dd($user);

        $age = rand(1,15);

        return view('cats.create',["url"=>$cat_data["url"],"user"=>$user,"breed"=>$breed,"age"=>$age]);
    }

    /**
     * 猫新規作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ''/*'required|string|max:20'*/,
            'name' => '',
            'gender'=>'',
            'age'=>'',
            'country'=>'',
            'breeds'=>'',
            'url'=>'',
        ]);
        $validated["message"] = "";

        $temp_path = tempnam(sys_get_temp_dir(), 'myApp_');

        //サムネイル用一時パス
        $thum_file_name = Str::random().".jpg";
        $thum_path = Storage::path('public/_thumbs/'.$thum_file_name);

        //画像取得
        $image = file_get_contents($validated["url"]);
        file_put_contents($temp_path , $image);

        //リサイズ
        $img = \Image::make($temp_path);
        $img->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($thum_path);

        $validated["thumb"] = $thum_file_name;

        //新規猫作成
        $request->user()->cats()->create($validated);

        return redirect(route('cats.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Cat $cat)
    {
        //
    }

    /**
     * 猫情報変更用ページ
     */
    public function edit(Cat $cat): View
    {
        $this->authorize('update', $cat);

        return view('cats.edit',["cat"=>$cat]);
    }

    /**
     * 猫情報変更
     */
    public function update(Request $request, Cat $cat): RedirectResponse
    {
        $this->authorize('update', $cat);

        $validated = $request->validate([
            'message' => '',
            'name' => '',
            'gender'=>'',
            'age'=>'',
            'country'=>'',
            'breeds'=>'',
            'url'=>'',
        ]);
        $validated["message"] = "";

        //dd($validated);

        $cat->update($validated);

        return redirect(route('cats.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cat $cat): RedirectResponse
    {
        $this->authorize('delete', $cat);

        $cat->delete();

        return redirect(route('cats.index'));
    }

    /**
     * 猫情報からWordテンプレートを使用してドキュメント作成
     */
    public function download(Request $request): BinaryFileResponse
    {
        //Wordテンプレートファイルパス
        $template_file_path= public_path(). "/doc/template.docx";

        $temp_path = $this->makeDocx($template_file_path,$request->user()->cats()->getResults());

        //ダウンロード
        $headers = array(
            'Content-Type: ' . mime_content_type( $temp_path ),
        );
        //return response()->download("document.pdf");
        return response()->download($temp_path, 'export.docx', $headers);
    }

    /**
     * Wordテンプレートに情報を埋め込む
     */
    private function makeDocx(string $originalPath,$cats): string
    {
        $templateProcessor = new TemplateProcessor($originalPath);

        //ブロック複製
        $templateProcessor->cloneBlock('BLOCK', $cats->count(),true,true);

        $cnt = 1;

        //情報埋め込み
        foreach($cats as $cat){
            $templateProcessor->setValue("name#$cnt",$cat->name);
            $templateProcessor->setValue("gender#$cnt",$cat->gender);
            $templateProcessor->setValue("age#$cnt",$cat->age);
            $templateProcessor->setValue("country#$cnt",$cat->country);
            $templateProcessor->setValue("breeds#$cnt",$cat->breeds);
            $templateProcessor->setValue("created_at#$cnt",$cat->created_at->format('M d, Y'));
            
            $thumb_path = Storage::path('public/_thumbs/'.$cat->thumb);
            $templateProcessor->setImageValue("img#$cnt",[
                "path"=> $this->getMonochroImg($thumb_path),
                "width"=>"400",
                "height"=>"400"
            ]);

            for($i = 1;$i<=4;$i++){
                $n = rand(1,20);
                $templateProcessor->setImageValue("stamp$i#$cnt",[
                    "path"=> public_path(). "/img/cat_stamps/$n.png",
                ]);
            }

            //改ページ挿入
            if($cnt != $cats->count()){
                $templateProcessor->setValue("PAGE_BREAK#{$cnt}", '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'. '<w:r><w:t>');
            }else{
                $templateProcessor->setValue("PAGE_BREAK#{$cnt}", '');
            }

            $cnt++;
        }

        $temp_path = $templateProcessor->save();       

        return $temp_path;
    }

    //写真モノクロ化化
    private function getMonochroImg(string $url){
        $temp_path = tempnam(sys_get_temp_dir(), 'myApp_');

        $img = \Image::make($url);
        $img->greyscale()->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($temp_path);

        return $temp_path;
    }

}
