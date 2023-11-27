import html2canvas from 'html2canvas'
import { jsPDF } from 'jspdf'
import pdfMake from 'pdfmake/build/pdfmake'

// 72dpi 時の mm => px 換算比
// 計算式は 1/(25.4mm / 72dpi)
const RATE = 2.83464566929

// A3 297mm x 419mm
const PAGE_WIDTH = 297 * RATE
const PAGE_HEIGHT = 419 * RATE

// TODO: 仮置き。後でページ設計に合わせて修正
const CONTENT_WIDTH = 297 * RATE
const CONTENT_HEIGHT = 419 * RATE
const PAGE_MARGINS = [0 * RATE, 0 * RATE]

/* for ts
interface PdfProps {
  dataUrl: string;
  pageSize?: {
    width: number;
    height: number;
  };
  pageOrientation?: string;
  contentSize?: {
    width: number;
    height: number;
  };
  pageMargins?: [number, number];
}
*/

/**
 * HTMLからPDFを生成
 * @param {HTMLElement} element
 */
export async function createPdfFromHtml(element, fileName) {
  console.log(fileName)
  // const pdfProps = await createPdfProps(element)
  // pdfProps.fileName = fileName
  // createPdf(pdfProps)
  captureHTMLtoA4Images(element).then((images) => {
    imagesToPDF(images, fileName)
  })
}

/**
 * PDF出力用のPdfPropsを作成
 * @param {HTMLElement} element
 * @param {boolean} isOnlyQrCode
 * @returns {Promise<PdfProps>}
 */
async function createPdfProps(element) {
  // html2canvas実行
  const options = {
    // HACK: ブラウザ依存でcanvasサイズが変わらないように、scaleは固定値。IEでのぼやけ対策で十分大きめの2にした
    scale: 3,
  }
  const canvas = await html2canvas(element, options)

  const dataUrl = canvas.toDataURL()

  const pdfProps = {
    dataUrl,
    pageSize: {
      width: PAGE_WIDTH,
      height: PAGE_HEIGHT,
    },
    pageOrientation: 'PORTRAIT',
    contentSize: {
      width: CONTENT_WIDTH,
      height: CONTENT_HEIGHT,
    },
    pageMargins: PAGE_MARGINS,
  }

  return pdfProps
}

/**
 * エンコードされた画像URLを貼り付けたPDFを出力する
 * @param {PdfProps} pdfProps
 */
function createPdf(pdfProps) {
  const { dataUrl, contentSize, pageMargins, fileName } = pdfProps
  console.log(fileName)
  // tsエラー回避のため一時的にany
  const pageSize = pdfProps.pageSize
  const pageOrientation = pdfProps.pageOrientation

  const documentDefinitions = {
    pageSize,
    pageOrientation,
    content: {
      image: dataUrl,
      ...contentSize,
    },
    pageMargins,
  }

  pdfMake.createPdf(documentDefinitions).download(fileName)
}

const captureHTMLtoA4Images = async (elementId) => {
  console.log(elementId)
  const A4_WIDTH = 1420 // A4 용지의 폭 (72 DPI 기준)
  const A4_HEIGHT = 1200 // A4 용지의 높이 (72 DPI 기준)

  const element = elementId
  const canvas = await html2canvas(element)

  const totalHeight = canvas.height
  const totalPages = Math.ceil(totalHeight / A4_HEIGHT)

  const images = []

  for (let i = 0; i < totalPages; i++) {
    const tempCanvas = document.createElement('canvas')
    tempCanvas.width = A4_WIDTH
    tempCanvas.height = A4_HEIGHT

    const context = tempCanvas.getContext('2d')
    context.drawImage(canvas, 0, i * A4_HEIGHT, A4_WIDTH, A4_HEIGHT, 0, 0, A4_WIDTH, A4_HEIGHT)

    images.push(tempCanvas.toDataURL('image/png'))
  }
  return images
}

// // 사용 예:
// captureHTMLtoA4Images('yourElementId').then(images => {
//   // images 배열에는 A4 크기의 이미지들이 포함되어 있습니다.
//   // images.forEach((imgSrc, index) => {
//   //   const img = new Image();
//   //   img.src = imgSrc;
//   //   document.body.appendChild(img);
//   // });
// });
function imagesToPDF(imageURLs, fileName) {
  const pdf = new jsPDF()

  imageURLs.forEach((url, index) => {
    const img = new Image()
    img.src = url
    img.onload = function () {
      const imgWidth = 306 // A4 width in mm
      const imgHeight = 300
      pdf.addImage(img, 'PNG', 0, 0, imgWidth, imgHeight)

      // 다음 이미지를 위해 페이지 추가 (마지막 이미지 제외)
      if (index !== imageURLs.length - 1) {
        pdf.addPage()
      }
    }
  })

  // 모든 이미지가 로드되고 PDF가 준비되면 저장
  setTimeout(() => {
    pdf.save(fileName)
  }, 1000)
}
