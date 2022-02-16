@include('layouts.head')

@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' => [
		    'breadcrumbs_items'=> $breadcrumbs,
		    'background' => $image_header ?? null
		]
	]
)

@component("blocks.title.section_title",
    [
        "data" =>[
            "section_class" => "section_vakans",
            "title" => "<h1>Доступные вакансии:</h1>",
            "section_class_title" => "title-gen"
        ]
    ])

    <br>

    <div class="container">
    {{--	<div class="jobs-excerpt">--}}
    {{--		{{$jobs[0]['excerpt']}}--}}
    {{--	</div>--}}

    <div class="about-olvia-jobs">
    <p>Преимущества работы в Аптечной сети Ольвия</p>
    <p class="about-text">Мы гарантируем интересную работу с перспективой карьерного роста и профессионального развития. Официальное
        трудоустройство, высокий уровень зарплаты, полный социальный пакет (оплата отпускных и больничных листов;
        ежегодный медосмотр за счёт компании). Возможности для карьерного и профессионального роста.</p>

    <p class="about-title">Нас отличают</p>

    <ul>
        <li>надежность;</li>
        <li>профессионализм;</li>
        <li>гарантия качества лекарственных средств и всей продукции;</li>
        <li>высокий уровень качества обслуживания;</li>
        <li>социальная ответственность.</li>
    </ul>

    <p class="about-title">Приглашаем в свою команду</p>
    <ul>
        <li>опытных провизоров и фармацевтов, желающих доказать свой профессионализм и заработать достойную оплату труда;</li>
        <li>молодых специалистов, желающих развиваться в крупной высокотехнологичной компании с перспективой карьерного роста;</li>
    </ul>
    <p>Мы будем рады приветствовать ВАС в нашей команде профессионалов!
        Нам доверяют самое ценное – здоровье!
    </p>
    <p>По вопросам трудоустройства обращайтесь по телефону: <a class="contact_link_hover" href="tel:tel:+380713617748">+38 (071) 361 77 48</a>, или отправляйте свое резюме на электронную почту:
    </p>
        <p>
            <a class="contact_link_hover" href="mailto:info@apteka-olvia.com.ua">info@apteka-olvia.com.ua</a>
        </p>
        </div>


    @php
        $sizes = array(
                        '1' => ['width' => 1922, 'relative_path' => 'uploads/'.$jobs[0]['image'], 'q'=> 90],
                        'id' => 'banner_01',
                      );
        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                      );
        $sizes = Img::img($sizes);

        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
    @endphp

    <div class="jobs-img Background_Is_Picture">
        {!! $picture !!}
    </div>

    {{--{{dd($jobs[0])}}--}}
    <div class="jobs-list">
        {!! $jobs[0]['body'] !!}
    </div>

    @endcomponent
    </div>
    @include('blocks.modal_basket.modal_basket')
    @include('blocks.popup.popup')
    @include('layouts.footer')