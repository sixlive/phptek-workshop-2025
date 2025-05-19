@if ($type === 'summary')
Please provide a comprehensive summary of this document. Focus on the main topic, key messages, and most important information.
@endif

@if ($type === 'key_points')
Please extract the key points from this document. Identify the most important information and organize it into clear, structured points.
@endif

@if('sentiment')
Please analyze the sentiment and emotional tone of this document. Identify the overall sentiment and specific emotions expressed in the content.
@endif
